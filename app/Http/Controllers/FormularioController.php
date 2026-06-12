<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;
use App\Models\Departamento;
use App\Models\Municipio;

class FormularioController extends Controller
{
    public function index($id = null)
    {
        $agenda = null;
        if ($id) {
            $agenda = AgendaDesplazamiento::with(['actividades', 'obligaciones', 'user', 'estado'])
                ->where('user_id', auth()->id())
                ->findOrFail($id);
        }

        $user = auth()->user()->load(['categoria.obligaciones', 'supervisor', 'ordenador']);
        
        $departamentos = Departamento::orderBy('nombre')->get();
        $clasificaciones = \App\Models\ClasificacionInformacion::all();
        $estados = \App\Models\EstadoAgenda::all();

        if ($user->role === 'funcionario') {
            return view('funcionario.formulario', compact('agenda', 'user', 'departamentos', 'clasificaciones', 'estados'));
        }

        return view('formulario', compact('agenda', 'user', 'departamentos', 'clasificaciones', 'estados'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'funcionario') {
            $rules = [
                'fecha_elaboracion' => 'required|date',
                'regional' => 'required|string|max:150',
                'centro_formacion' => 'required|string|max:150',
                'destinos' => 'required|array|min:1',
                'destinos.*.departamento_id' => 'required|exists:departamentos,id',
                'destinos.*.municipio_id' => 'nullable|exists:municipios,id',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'objetivo_desplazamiento' => 'required|string|max:1000',
            ];
        } else {
            $rules = [
                'fecha_elaboracion' => 'required|date',
                'clasificacion_id' => 'required|exists:clasificacion_informacion,id',
                'regional' => 'required|string|max:150',
                'centro' => 'required|string|max:150',
                'destinos' => 'required|array|min:1',
                'destinos.*.departamento_id' => 'required|exists:departamentos,id',
                'destinos.*.municipio_id' => 'nullable|exists:municipios,id',
                'destinos.*.vereda' => 'nullable|string|max:120',
                'entidad_empresa' => 'required|string|max:120',
                'contacto' => 'required|string|max:120',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'objetivo_desplazamiento' => 'required|string|max:1000',
                'obligaciones' => 'required|array|min:1',
                'obligaciones.*' => 'required|exists:obligaciones_contrato,id',
            ];
        }

        // Solo exigir que la fecha sea futura si es una agenda nueva
        if (!$request->filled('agenda_id')) {
            $rules['fecha_inicio'] .= '|after_or_equal:today';
        }

        $validatedData = $request->validate($rules);

        /* ================= DATOS CALCULADOS ================= */
        $rutaItems = ['MEDELLIN'];
        $destinosData = [];

        foreach ($validatedData['destinos'] as $dest) {
            $depto = Departamento::find($dest['departamento_id']);
            $muni = $dest['municipio_id'] ? Municipio::find($dest['municipio_id']) : null;
            $nombreDestino = $muni ? $muni->nombre : $depto->nombre;

            $rutaItems[] = $nombreDestino;
            if (!empty($dest['vereda'] ?? null)) {
                $rutaItems[] = $dest['vereda'];
            }

            $destinosData[] = [
                'departamento_id' => $dest['departamento_id'],
                'municipio_id' => $dest['municipio_id'],
                'vereda' => $dest['vereda'] ?? null,
                'nombre' => $nombreDestino
            ];
        }

        // Reversa para la ruta
        $reversa = array_reverse($rutaItems);
        // Evitar duplicar el último destino si ya está ahí (ej: MEDELLIN - BELLO - BELLO - MEDELLIN)
        // El usuario quiere: MEDELLIN - A - B - B - A - MEDELLIN
        $fullRutaArray = array_merge($rutaItems, $reversa);
        $ruta = implode(' - ', $fullRutaArray);

        $estadoInicial = \App\Models\EstadoAgenda::where('nombre', 'BORRADOR')->first();

        $agendaData = [
            'user_id' => $user->id,
            'clasificacion_id' => $validatedData['clasificacion_id'] ?? 1, // Default para funcionarios
            'estado_id' => $estadoInicial->id,
            'fecha_elaboracion' => $validatedData['fecha_elaboracion'],
            'ruta' => $ruta,
            'regional' => $validatedData['regional'],
            'centro' => $validatedData['centro'] ?? $validatedData['centro_formacion'], // Mapeo dinámico
            'destinos' => $destinosData,
            'entidad_empresa' => $validatedData['entidad_empresa'] ?? 'N/A',
            'contacto' => $validatedData['contacto'] ?? 'N/A',
            'objetivo_desplazamiento' => $validatedData['objetivo_desplazamiento'],
            'fecha_inicio' => $validatedData['fecha_inicio'],
            'fecha_fin' => $validatedData['fecha_fin'],
            'supervisor_id' => $user->supervisor_id,
            'ordenador_id' => $user->ordenador_id,
        ];

        $agenda = $request->filled('agenda_id')
            ? AgendaDesplazamiento::where('user_id', $user->id)->findOrFail($request->agenda_id)
            : new AgendaDesplazamiento();

        // Si es una actualización, conservamos el estado si no es borrador o lo reseteamos si es corrección
        if ($request->filled('agenda_id')) {
            $agenda->observaciones_finanzas = null;
        }

        $agenda->fill($agendaData);
        $agenda->save();

        // Sincronizar Obligaciones (Solo si aplican)
        if (isset($validatedData['obligaciones'])) {
            $agenda->obligaciones()->sync($validatedData['obligaciones']);
        }

        if ($request->filled('agenda_id')) {
            return redirect()->to(session('back_url_reportar_dia', route('inicio')))
                ->with('success', 'La agenda se corrigió con éxito.');
        }

        return redirect()->route('reportar-dia.show', $agenda->id)
            ->with('success', 'Agenda creada correctamente. Ahora puede reportar sus actividades para cada día.');
    }

    public function pdf($id)
    {
        $agenda = AgendaDesplazamiento::with([
            'actividades', 
            'obligaciones', 
            'user', 
            'supervisor', 
            'ordenador', 
            'estado',
            'clasificacion'
        ])->findOrFail($id);

        $user = auth()->user();
        
        // --- VALIDACIÓN DE SEGURIDAD (CANDADO) ---
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
        $funcionarioId = $funcionario ? $funcionario->id : null;

        $isAuthorized = ($user->role === 'administrador') ||
                        ($user->role === 'viaticos') ||
                        ($user->role === 'legalizacion') ||
                        ($agenda->user_id === $user->id) ||
                        ($funcionarioId && ($agenda->supervisor_id === $funcionarioId || $agenda->ordenador_id === $funcionarioId));

        if (!$isAuthorized) {
            abort(403, 'No tiene permisos para acceder a este documento.');
        }
        // -----------------------------------------

        // --- VALIDACIÓN DE PDF PRE-GUARDADO (ESTADO FINAL) ---
        $isFinalState = ($agenda->estado && $agenda->estado->nombre === 'APROBADA');
        if ($isFinalState) {
            $filePath = storage_path('app/final_pdfs/agenda_' . $id . '_' . $agenda->user->numero_documento . '.pdf');
            if (file_exists($filePath)) {
                return response()->file($filePath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="Agenda_Desplazamiento_' . $id . '.pdf"'
                ]);
            }
        }

        $view = ($agenda->user && $agenda->user->role === 'funcionario') ? 'funcionario.pdf' : 'agenda.pdf';

        return view($view, compact('agenda', 'isFinalState'));
    }

    public function enviar($id)
    {
        $agenda = AgendaDesplazamiento::where('user_id', auth()->id())->findOrFail($id);

        if ($agenda->estado->nombre !== 'BORRADOR') {
            return back()->with('error', 'Esta agenda ya ha sido enviada o procesada.');
        }

        $estadoEnviada = \App\Models\EstadoAgenda::where('nombre', 'ENVIADA')->first();
        $agenda->update(['estado_id' => $estadoEnviada->id]);

        return redirect()->route('inicio')->with('success', 'Agenda enviada a coordinación para revisión.');
    }

    public function crearLegalizacion(AgendaDesplazamiento $agenda)
    {
        $user = auth()->user();
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
        $funcionarioId = $funcionario ? $funcionario->id : null;

        $isAuthorized = ($user->role === 'administrador') ||
                        ($agenda->user_id === $user->id) ||
                        ($funcionarioId && ($agenda->supervisor_id === $funcionarioId || $agenda->ordenador_id === $funcionarioId));

        if (!$isAuthorized) {
            abort(403, 'No tiene permisos para acceder a esta agenda.');
        }

        // Se puede legalizar solo si está aprobada
        if ($agenda->estado?->nombre !== 'APROBADA') {
            return redirect()->route('inicio')->with('error', 'Solo se pueden legalizar agendas con estado APROBADA.');
        }

        // Si ya está en un estado de aprobación que no permite edición
        if (!in_array($agenda->legalizacion_estado, [null, 'CREADA', 'BORRADOR', 'DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR'])) {
            return redirect()->route('inicio')->with('error', 'La legalización ya fue enviada y no puede ser modificada.');
        }

        $clasificaciones = \App\Models\ClasificacionInformacion::all();
        return view('legalizacion.crear', compact('agenda', 'clasificaciones'));
    }

    public function guardarLegalizacion(Request $request, AgendaDesplazamiento $agenda)
    {
        // Detect if request exceeded post_max_size (e.g. they uploaded a 220MB file but server limit is 40MB)
        if ($request->isMethod('post') && empty($request->all()) && $request->headers->has('content-length') && $request->header('content-length') > 0) {
            return back()->withInput()->withErrors([
                'archivo' => 'La solicitud de subida supera el límite máximo permitido por el servidor (40 MB). Por favor, seleccione archivos más pequeños.'
            ]);
        }

        $user = auth()->user();
        if ($agenda->user_id !== $user->id && $user->role !== 'administrador') {
            abort(403, 'No tiene permisos para acceder a esta agenda.');
        }

        $comisionado = $agenda->user;
        $isFuncionario = ($comisionado && $comisionado->role === 'funcionario');
        $realiza_declaracion = $request->boolean('realiza_declaracion');

        // Extra backend validation check for file sizes and upload errors (10 MB limit)
        $maxSizeBytes = 10 * 1024 * 1024; // 10MB
        $fieldsToCheck = ['fotos', 'planillas', 'tiquetes'];
        foreach ($fieldsToCheck as $field) {
            if ($request->hasFile($field) || $request->file($field)) {
                $files = $request->file($field);
                $filesArray = is_array($files) ? $files : [$files];
                foreach ($filesArray as $file) {
                    if ($file) {
                        if (!$file->isValid()) {
                            $errorMsg = $file->getError() == UPLOAD_ERR_INI_SIZE 
                                ? "Uno o más archivos en " . ($field === 'fotos' ? 'evidencias fotográficas' : ($field === 'planillas' ? 'planillas de asistencia' : 'tiquetes')) . " superan el límite de subida permitido por el servidor."
                                : "Uno o más archivos en " . ($field === 'fotos' ? 'evidencias fotográficas' : ($field === 'planillas' ? 'planillas de asistencia' : 'tiquetes')) . " no se cargaron correctamente.";
                            return back()->withInput()->withErrors([$field => $errorMsg]);
                        }
                        if ($file->getSize() > $maxSizeBytes) {
                            return back()->withInput()->withErrors([
                                $field => "Uno o más archivos en " . ($field === 'fotos' ? 'evidencias fotográficas' : ($field === 'planillas' ? 'planillas de asistencia' : 'tiquetes')) . " superan el límite de 10 MB."
                            ]);
                        }
                    }
                }
            }
        }
        
        if ($request->hasFile('declaracion') || $request->file('declaracion')) {
            $file = $request->file('declaracion');
            if ($file) {
                if (!$file->isValid()) {
                    $errorMsg = $file->getError() == UPLOAD_ERR_INI_SIZE 
                        ? "El archivo de declaración no juramentada supera el límite de subida permitido por el servidor."
                        : "El archivo de declaración no juramentada no se cargó correctamente.";
                    return back()->withInput()->withErrors(['declaracion' => $errorMsg]);
                }
                if ($file->getSize() > $maxSizeBytes) {
                    return back()->withInput()->withErrors([
                        'declaracion' => "El archivo de declaración no juramentada supera el límite de 10 MB."
                    ]);
                }
            }
        }

        // Validation rules
        if ($isFuncionario) {
            $rules = [
                'orden_viaje' => 'required|string|max:100',
                'clasificacion_id' => 'required|exists:clasificacion_informacion,id',
                'realiza_declaracion' => 'required|boolean',
                'fotos' => ($agenda->legalizacion_fotos ? 'nullable' : 'required') . '|array|min:1',
                'fotos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
                'planillas' => ($agenda->legalizacion_planillas ? 'nullable' : 'required') . '|array|min:1',
                'planillas.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
                'resultados' => 'required|array|min:1',
                'resultados.*' => 'required|string|max:1000',
                'soportes_desplazamiento' => 'required|array|min:1',
                'soportes_desplazamiento.*' => 'required|string|max:1000',
            ];

            if ($realiza_declaracion) {
                $rules['legalizacion_codigo_regional'] = 'required|string|max:20';
                $rules['legalizacion_codigo_centro'] = 'required|string|max:20';
                $rules['declaracion'] = 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240';
                $rules['gastos_transporte'] = 'nullable|array';
                $rules['gastos_transporte.*.fecha'] = 'required|date';
                $rules['gastos_transporte.*.trayecto'] = 'required|string|max:255';
                $rules['gastos_transporte.*.medio'] = 'required|string|in:BUS,BARCO,AVION';
                $rules['gastos_transporte.*.valor'] = 'required|numeric|min:0';
            } else {
                $hasTiquetes = !empty($agenda->legalizacion_tiquetes);
                $rules['tiquetes'] = ($hasTiquetes ? 'nullable' : 'required') . '|array|min:1';
                $rules['tiquetes.*'] = 'required|image|mimes:jpeg,png,jpg,gif|max:10240';
            }
        } else {
            $rules = [
                'orden_viaje' => 'required|string|max:100',
                'realiza_declaracion' => 'required|boolean',
                'fotos' => ($agenda->legalizacion_fotos ? 'nullable' : 'required') . '|array|min:1',
                'fotos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
                'planillas' => ($agenda->legalizacion_planillas ? 'nullable' : 'required') . '|array|min:1',
                'planillas.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
                'resultados' => 'required|array|min:1',
                'resultados.*' => 'required|string|max:1000',
                'conclusiones' => 'required|array|min:1',
                'conclusiones.*' => 'required|string|max:1000',
                'compromisos' => 'required|array|min:1',
                'compromisos.*.actividad' => 'required|string|max:1000',
                'compromisos.*.responsable' => 'required|string|max:255',
                'compromisos.*.fecha' => 'required|date',
            ];

            if ($realiza_declaracion) {
                $rules['legalizacion_codigo_regional'] = 'required|string|max:20';
                $rules['legalizacion_codigo_centro'] = 'required|string|max:20';
                $rules['declaracion'] = 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240';
                $rules['gastos_transporte'] = 'nullable|array';
                $rules['gastos_transporte.*.fecha'] = 'required|date';
                $rules['gastos_transporte.*.trayecto'] = 'required|string|max:255';
                $rules['gastos_transporte.*.medio'] = 'required|string|in:BUS,BARCO,AVION';
                $rules['gastos_transporte.*.valor'] = 'required|numeric|min:0';
            } else {
                $hasTiquetes = !empty($agenda->legalizacion_tiquetes);
                $rules['tiquetes'] = ($hasTiquetes ? 'nullable' : 'required') . '|array|min:1';
                $rules['tiquetes.*'] = 'required|image|mimes:jpeg,png,jpg,gif|max:10240';
            }
        }

        $request->validate($rules, [
            'clasificacion_id.required' => 'La clasificación de la información es obligatoria.',
            'clasificacion_id.exists' => 'La clasificación de la información seleccionada no es válida.',
            'soportes_desplazamiento.required' => 'Debe registrar al menos un soporte de desplazamiento.',
            'soportes_desplazamiento.*.required' => 'El soporte de desplazamiento no puede estar vacío.',
            'orden_viaje.required' => 'El número de ORDEN DE VIAJE No es obligatorio.',
            'realiza_declaracion.required' => 'Debe responder si desea realizar la declaración no juramentada.',
            'legalizacion_codigo_regional.required' => 'El Código Regional es obligatorio.',
            'legalizacion_codigo_centro.required' => 'El Código de Centro es obligatorio.',
            'fotos.required' => 'Debe seleccionar las fotos de evidencia.',
            'fotos.min' => 'Debe subir al menos 1 foto de evidencia.',
            'fotos.*.image' => 'Los archivos de fotos deben ser imágenes.',
            'fotos.*.max' => 'Cada foto de evidencia no debe superar los 10 MB.',
            'planillas.required' => 'Debe seleccionar las fotos del listado de planillas.',
            'planillas.min' => 'Debe subir al menos 1 planilla.',
            'planillas.*.image' => 'Los archivos de planillas deben ser imágenes.',
            'planillas.*.max' => 'Cada planilla de asistencia no debe superar los 10 MB.',
            'declaracion.file' => 'La declaración no juramentada debe ser un archivo válido.',
            'declaracion.mimes' => 'La declaración no juramentada debe ser un archivo de tipo: jpeg, png, jpg, gif, pdf.',
            'declaracion.max' => 'La declaración no juramentada no debe superar los 10 MB.',
            'tiquetes.required' => 'Debe adjuntar al menos un tiquete del trayecto.',
            'tiquetes.min' => 'Debe adjuntar al menos un tiquete del trayecto.',
            'tiquetes.*.required' => 'Los tiquetes son obligatorios.',
            'tiquetes.*.image' => 'Los tiquetes deben ser imágenes.',
            'tiquetes.*.mimes' => 'Los tiquetes deben ser de tipo: jpeg, png, jpg, gif.',
            'tiquetes.*.max' => 'Los tiquetes no deben superar los 10 MB.',
            'resultados.required' => 'Debe registrar al menos un resultado.',
            'resultados.min' => 'Debe registrar al menos un resultado.',
            'resultados.*.required' => 'El campo de resultado no puede estar vacío.',
            'conclusiones.required' => 'Debe registrar al menos una conclusión.',
            'conclusiones.min' => 'Debe registrar al menos una conclusión.',
            'conclusiones.*.required' => 'El campo de conclusión no puede estar vacío.',
            'compromisos.required' => 'Debe registrar al menos un compromiso.',
            'compromisos.min' => 'Debe registrar al menos un compromiso.',
            'compromisos.*.actividad.required' => 'La actividad del compromiso es obligatoria.',
            'compromisos.*.responsable.required' => 'El responsable del compromiso es obligatorio.',
            'compromisos.*.fecha.required' => 'La fecha del compromiso es obligatoria.',
            'compromisos.*.fecha.date' => 'La fecha del compromiso debe ser válida.',
            'gastos_transporte.*.fecha.required' => 'La fecha de cada gasto de transporte es obligatoria.',
            'gastos_transporte.*.trayecto.required' => 'El trayecto de cada gasto de transporte es obligatorio.',
            'gastos_transporte.*.medio.required' => 'El medio de transporte es obligatorio.',
            'gastos_transporte.*.medio.in' => 'El medio de transporte seleccionado no es válido.',
            'gastos_transporte.*.valor.required' => 'El valor pagado de cada gasto de transporte es obligatorio.',
            'gastos_transporte.*.valor.numeric' => 'El valor pagado debe ser numérico.',
            'gastos_transporte.*.valor.min' => 'El valor pagado no puede ser menor a 0.',
        ]);

        $fotosPaths = [];
        if ($request->hasFile('fotos')) {
            if (!empty($agenda->legalizacion_fotos)) {
                foreach ($agenda->legalizacion_fotos as $oldFoto) {
                    if (\Storage::disk('public')->exists($oldFoto)) {
                        \Storage::disk('public')->delete($oldFoto);
                    }
                }
            }
            foreach ($request->file('fotos') as $file) {
                $path = $file->store('legalizacion/fotos', 'public');
                $fotosPaths[] = $path;
            }
        } else {
            $fotosPaths = $agenda->legalizacion_fotos ?? [];
        }

        $planillasPaths = [];
        if ($request->hasFile('planillas')) {
            if (!empty($agenda->legalizacion_planillas)) {
                foreach ($agenda->legalizacion_planillas as $oldPlanilla) {
                    if (\Storage::disk('public')->exists($oldPlanilla)) {
                        \Storage::disk('public')->delete($oldPlanilla);
                    }
                }
            }
            foreach ($request->file('planillas') as $file) {
                $path = $file->store('legalizacion/planillas', 'public');
                $planillasPaths[] = $path;
            }
        } else {
            $planillasPaths = $agenda->legalizacion_planillas ?? [];
        }

        $declaracionPath = $realiza_declaracion ? $agenda->legalizacion_declaracion : null;
        if ($realiza_declaracion) {
            if ($request->hasFile('declaracion')) {
                if ($agenda->legalizacion_declaracion && \Storage::disk('public')->exists($agenda->legalizacion_declaracion)) {
                    \Storage::disk('public')->delete($agenda->legalizacion_declaracion);
                }
                $declaracionPath = $request->file('declaracion')->store('legalizacion/declaraciones', 'public');
            }
        } else {
            if ($agenda->legalizacion_declaracion && \Storage::disk('public')->exists($agenda->legalizacion_declaracion)) {
                \Storage::disk('public')->delete($agenda->legalizacion_declaracion);
            }
        }

        $tiquetesPaths = !$realiza_declaracion ? ($agenda->legalizacion_tiquetes ?? []) : [];
        if (!$realiza_declaracion) {
            if ($request->hasFile('tiquetes')) {
                if (!empty($agenda->legalizacion_tiquetes)) {
                    foreach ($agenda->legalizacion_tiquetes as $oldTiquete) {
                        if (\Storage::disk('public')->exists($oldTiquete)) {
                            \Storage::disk('public')->delete($oldTiquete);
                        }
                    }
                }
                $tiquetesPaths = [];
                foreach ($request->file('tiquetes') as $file) {
                    $path = $file->store('legalizacion/tiquetes', 'public');
                    $tiquetesPaths[] = $path;
                }
            }
        } else {
            if (!empty($agenda->legalizacion_tiquetes)) {
                foreach ($agenda->legalizacion_tiquetes as $oldTiquete) {
                    if (\Storage::disk('public')->exists($oldTiquete)) {
                        \Storage::disk('public')->delete($oldTiquete);
                    }
                }
            }
        }

        $nuevoEstado = $agenda->legalizacion_estado ?: 'BORRADOR';
        if (str_starts_with($agenda->legalizacion_estado ?? '', 'DEVUELTA_')) {
            $nuevoEstado = 'BORRADOR';
        }

        $agenda->update([
            'orden_viaje' => $request->orden_viaje,
            'clasificacion_id' => $isFuncionario ? $request->clasificacion_id : $agenda->clasificacion_id,
            'realiza_declaracion' => $realiza_declaracion,
            'legalizacion_codigo_regional' => $realiza_declaracion ? $request->legalizacion_codigo_regional : null,
            'legalizacion_codigo_centro' => $realiza_declaracion ? $request->legalizacion_codigo_centro : null,
            'legalizacion_gastos_transporte' => $realiza_declaracion ? ($request->gastos_transporte ?? []) : [],
            'legalizacion_fotos' => $fotosPaths,
            'legalizacion_planillas' => $planillasPaths,
            'legalizacion_declaracion' => $declaracionPath,
            'legalizacion_tiquetes' => $tiquetesPaths,
            'legalizacion_resultados' => $request->resultados,
            'legalizacion_compromisos' => $isFuncionario ? [] : $request->compromisos,
            'legalizacion_conclusiones' => $isFuncionario ? [] : $request->conclusiones,
            'legalizacion_soportes_desplazamiento' => $isFuncionario ? ($request->soportes_desplazamiento ?? []) : [],
            'legalizado_at' => now(),
            'legalizacion_estado' => $nuevoEstado,
            'legalizacion_observaciones' => null,
            'legalizacion_firma_comisionado_path' => $agenda->firma_contratista_path ?: $user->firma,
        ]);

        return redirect()->route('inicio')->with('success', 'La legalización se guardó en Borrador correctamente. Recuerde enviarla para revisión.');
    }

    public function enviarLegalizacion($id)
    {
        $agenda = AgendaDesplazamiento::where('user_id', auth()->id())->findOrFail($id);

        if (!in_array($agenda->legalizacion_estado, ['CREADA', 'BORRADOR', 'DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR'])) {
            return back()->with('error', 'La legalización no está en un estado que permita el envío.');
        }

        $agenda->update([
            'legalizacion_estado' => 'ENVIADA',
            'legalizacion_observaciones' => null,
            'legalizacion_firma_comisionado_path' => $agenda->firma_contratista_path ?: auth()->user()->firma
        ]);

        return redirect()->route('inicio')->with('success', 'Legalización enviada a coordinación para revisión.');
    }

    public function verLegalizacion(AgendaDesplazamiento $agenda)
    {
        $user = auth()->user();
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
        $funcionarioId = $funcionario ? $funcionario->id : null;

        $isAuthorized = ($user->role === 'administrador') ||
                        ($user->role === 'viaticos') ||
                        ($user->role === 'legalizacion') ||
                        ($agenda->user_id === $user->id) ||
                        ($funcionarioId && ($agenda->supervisor_id === $funcionarioId || $agenda->ordenador_id === $funcionarioId));

        if (!$isAuthorized) {
            abort(403, 'No tiene permisos para acceder a este documento.');
        }

        // Si no está legalizado
        if (empty($agenda->legalizacion_estado)) {
            return redirect()->route('inicio')->with('error', 'Esta agenda no cuenta con una legalización registrada.');
        }

        // --- VALIDACIÓN DE PDF PRE-GUARDADO (ESTADO FINAL) ---
        $isFinalState = ($agenda->legalizacion_estado === 'APROBADA_ORDENADOR');
        if ($isFinalState) {
            $filePath = storage_path('app/final_pdfs/legalizacion_' . $agenda->id . '_' . $agenda->user->numero_documento . '.pdf');
            if (file_exists($filePath)) {
                return response()->file($filePath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="Legalizacion_' . $agenda->id . '.pdf"'
                ]);
            }
        }

        // Cargar firmas en Base64 para visualización en el PDF
        $logoBase64 = '';
        $posiblesRutas = [
            public_path('images/sena/logoSena.png'),
            public_path('images/sena/logo-sena-verde.png'),
            public_path('images/sena/agenda_labores.png'),
            base_path('../public_html/images/sena/logoSena.png'),
            base_path('../public_html/images/sena/logo-sena-verde.png'),
            $_SERVER['DOCUMENT_ROOT'] . '/images/sena/logoSena.png',
            $_SERVER['DOCUMENT_ROOT'] . '/images/sena/logo-sena-verde.png'
        ];
        foreach ($posiblesRutas as $ruta) {
            if (file_exists($ruta)) {
                $logoData = file_get_contents($ruta);
                $mimeType = mime_content_type($ruta) ?: 'image/png';
                $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($logoData);
                break;
            }
        }
        if (empty($logoBase64)) {
            $logoBase64 = asset('images/sena/logoSena.png');
        }

        $contratista_firma_base64 = null;
        $pathFirma = $agenda->legalizacion_firma_comisionado_path ?: ($agenda->firma_contratista_path ?: ($agenda->user->firma ?? null));
        if ($pathFirma) {
            $pathFirmaFull = storage_path('app/public/' . $pathFirma);
            if (file_exists($pathFirmaFull)) {
                $contratista_firma_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($pathFirmaFull));
            }
        }

        $supervisor_firma_base64 = null;
        if (in_array($agenda->legalizacion_estado, ['APROBADA_SUPERVISOR', 'APROBADA_LEGALIZACION', 'APROBADA_ORDENADOR'])) {
            $pathFirmaSuper = $agenda->legalizacion_firma_supervisor_path ?: ($agenda->supervisor->firma ?? null);
            if ($pathFirmaSuper) {
                $pathFirmaSuperFull = storage_path('app/public/' . $pathFirmaSuper);
                if (file_exists($pathFirmaSuperFull)) {
                    $supervisor_firma_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($pathFirmaSuperFull));
                }
            }
        }

        $ordenador_firma_base64 = null;
        if ($agenda->legalizacion_estado === 'APROBADA_ORDENADOR' && $agenda->realiza_declaracion) {
            $pathFirmaOrdenador = $agenda->legalizacion_firma_ordenador_path ?: ($agenda->ordenador->firma ?? null);
            if ($pathFirmaOrdenador) {
                $pathFirmaOrdenadorFull = storage_path('app/public/' . $pathFirmaOrdenador);
                if (file_exists($pathFirmaOrdenadorFull)) {
                    $ordenador_firma_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($pathFirmaOrdenadorFull));
                }
            }
        }

        $view = ($agenda->user && $agenda->user->role === 'funcionario') 
            ? 'legalizacion.pdflegalizacion_funcionario' 
            : 'legalizacion.pdflegalizacion';

        return view($view, compact('agenda', 'logoBase64', 'contratista_firma_base64', 'supervisor_firma_base64', 'ordenador_firma_base64', 'isFinalState'));
    }

    public function saveAgendaPdf(Request $request, $id)
    {
        $agenda = AgendaDesplazamiento::with('user')->findOrFail($id);
        $user = auth()->user();
        
        // --- VALIDACIÓN DE SEGURIDAD (CANDADO) ---
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
        $funcionarioId = $funcionario ? $funcionario->id : null;

        $isAuthorized = ($user->role === 'administrador') ||
                        ($user->role === 'viaticos') ||
                        ($user->role === 'legalizacion') ||
                        ($agenda->user_id === $user->id) ||
                        ($funcionarioId && ($agenda->supervisor_id === $funcionarioId || $agenda->ordenador_id === $funcionarioId));

        if (!$isAuthorized) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if (!$request->hasFile('pdf')) {
            return response()->json(['error' => 'No se recibió ningún archivo PDF'], 400);
        }

        $dir = storage_path('app/final_pdfs');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $request->file('pdf')->move($dir, 'agenda_' . $id . '_' . $agenda->user->numero_documento . '.pdf');

        return response()->json(['success' => true]);
    }

    public function saveLegalizacionPdf(Request $request, $id)
    {
        $agenda = AgendaDesplazamiento::with('user')->findOrFail($id);
        $user = auth()->user();

        // --- VALIDACIÓN DE SEGURIDAD (CANDADO) ---
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
        $funcionarioId = $funcionario ? $funcionario->id : null;

        $isAuthorized = ($user->role === 'administrador') ||
                        ($user->role === 'viaticos') ||
                        ($user->role === 'legalizacion') ||
                        ($agenda->user_id === $user->id) ||
                        ($funcionarioId && ($agenda->supervisor_id === $funcionarioId || $agenda->ordenador_id === $funcionarioId));

        if (!$isAuthorized) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if (!$request->hasFile('pdf')) {
            return response()->json(['error' => 'No se recibió ningún archivo PDF'], 400);
        }

        $dir = storage_path('app/final_pdfs');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $request->file('pdf')->move($dir, 'legalizacion_' . $id . '_' . $agenda->user->numero_documento . '.pdf');

        return response()->json(['success' => true]);
    }
}

