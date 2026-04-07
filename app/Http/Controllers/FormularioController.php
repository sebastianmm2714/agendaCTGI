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
            $agenda = AgendaDesplazamiento::with(['actividades', 'obligaciones', 'user'])->findOrFail($id);
        }

        $user = auth()->user()->load(['categoria.obligaciones', 'supervisor', 'ordenador']);
        
        $departamentos = Departamento::orderBy('nombre')->get();
        $clasificaciones = \App\Models\ClasificacionInformacion::all();
        $estados = \App\Models\EstadoAgenda::all();

        return view('formulario', compact('agenda', 'user', 'departamentos', 'clasificaciones', 'estados'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

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

            'objetivo_desplazamiento' => 'required|string|max:160',

            'obligaciones' => 'required|array|min:1',
            'obligaciones.*' => 'required|exists:obligaciones_contrato,id',
        ];

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
            if (!empty($dest['vereda'])) {
                $rutaItems[] = $dest['vereda'];
            }

            $destinosData[] = [
                'departamento_id' => $dest['departamento_id'],
                'municipio_id' => $dest['municipio_id'],
                'vereda' => $dest['vereda'],
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
            'clasificacion_id' => $validatedData['clasificacion_id'],
            'estado_id' => $estadoInicial->id,
            'fecha_elaboracion' => $validatedData['fecha_elaboracion'],
            'ruta' => $ruta,
            'regional' => $validatedData['regional'],
            'centro' => $validatedData['centro'],
            'destinos' => $destinosData,
            'entidad_empresa' => $validatedData['entidad_empresa'],
            'contacto' => $validatedData['contacto'],
            'objetivo_desplazamiento' => $validatedData['objetivo_desplazamiento'],
            'fecha_inicio' => $validatedData['fecha_inicio'],
            'fecha_fin' => $validatedData['fecha_fin'],
            'supervisor_id' => $user->supervisor_id,
            'ordenador_id' => $user->ordenador_id,
        ];

        $agenda = $request->filled('agenda_id')
            ? AgendaDesplazamiento::findOrFail($request->agenda_id)
            : new AgendaDesplazamiento();

        // Si es una actualización, conservamos el estado si no es borrador o lo reseteamos si es corrección
        if ($request->filled('agenda_id')) {
            $agenda->observaciones_finanzas = null;
        }

        $agenda->fill($agendaData);
        $agenda->save();

        // Sincronizar Obligaciones
        $agenda->obligaciones()->sync($validatedData['obligaciones']);

        if ($request->filled('agenda_id')) {
            return redirect()->route('reportar-dia')
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
        return view('agenda.pdf', compact('agenda'));
    }

    public function enviar($id)
    {
        $agenda = AgendaDesplazamiento::where('user_id', auth()->id())->findOrFail($id);

        if ($agenda->estado->nombre !== 'BORRADOR') {
            return back()->with('error', 'Esta agenda ya ha sido enviada o procesada.');
        }

        $estadoEnviada = \App\Models\EstadoAgenda::where('nombre', 'ENVIADA')->first();
        $agenda->update(['estado_id' => $estadoEnviada->id]);

        return redirect()->route('reportar-dia')->with('success', 'Agenda enviada a coordinación para revisión.');
    }

}

