<?php

namespace App\Http\Controllers;

use App\Models\AgendaActividad;
use App\Models\AgendaDesplazamiento;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    /**
     * Punto de entrada único para "Reportes"
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // CASO 1: Si el usuario es de VIÁTICOS
        if ($user->role == 'viaticos') {
            return redirect()->route('inicio');
        }

        $activeTab = $request->get('tab', 'pendientes');
        $perPage   = (int) $request->get('per_page', 6);
        $search    = $request->get('search');

        $query = AgendaDesplazamiento::with(['user.categoria', 'estado', 'actividades'])
            ->orderBy('updated_at', 'desc');

        // Búsqueda inteligente
        if ($request->filled('search')) {
            $query->where(function($q) use ($search) {
                $searchEscaped = str_replace(['%', '_'], ['\%', '\_'], $search);
                $q->whereHas('user', function($qu) use ($searchEscaped) {
                    $qu->where('name', 'like', "%$searchEscaped%")
                       ->orWhere('numero_documento', 'like', "%$searchEscaped%");
                })
                ->orWhere('ruta', 'like', "%$searchEscaped%")
                ->orWhere('destinos', 'like', "%$searchEscaped%")
                ->orWhereRaw("DATE_FORMAT(fecha_inicio, '%d/%m/%Y') LIKE ?", ["%$searchEscaped%"])
                ->orWhereRaw("DATE_FORMAT(fecha_fin, '%d/%m/%Y') LIKE ?", ["%$searchEscaped%"]);
            });
        }

        // Lógica por Rol (Unificación con Dashboard para Contratistas y Funcionarios)
        if ($user->role == 'contratista' || $user->role == 'funcionario') {
            // Mapeo de pestañas de reportes a filtros de inicio
            $map = [
                'pendientes' => 'pendientes',
                'aprobadas' => 'enviadas',
                'devueltas' => 'devueltas'
            ];
            $ver = $map[$activeTab] ?? 'pendientes';
            
            return redirect()->route('inicio', [
                'ver' => $ver,
                'search' => $search,
                'per_page' => $perPage
            ]);
        } elseif ($user->role == 'supervisor_contrato') {
            $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
            if (!$funcionario) return view('reportes.index', ['agendas' => collect(), 'estados' => collect()]);

            $query->where('supervisor_id', $funcionario->id)
                  ->whereHas('estado', function($q) {
                      $q->where('nombre', '!=', 'BORRADOR');
                  });
        } elseif ($user->role == 'ordenador_gasto') {
            $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
            if (!$funcionario) return view('reportes.index', ['agendas' => collect(), 'estados' => collect()]);

            $query->where('ordenador_id', $funcionario->id)
                  ->whereHas('estado', function($q) {
                      $q->where('nombre', '!=', 'BORRADOR');
                  });
        }

        // Para otros roles (Admin/Supervisor/Ordenador) mantenemos la vista clásica de tabla única por ahora o adaptamos
        if ($user->role == 'administrador') {
             $query->whereHas('estado', function($q) {
                 $q->where('nombre', '!=', 'BORRADOR');
             });
        }

        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        $agendas = $query->paginate($perPage)->appends($request->all());
        session(['back_url_reportar_dia' => $request->fullUrl()]);
        
        $estados = \App\Models\EstadoAgenda::where('nombre', '!=', 'BORRADOR')->get();

        return view('reportes.index', compact('agendas', 'estados'));
    }

    /**
     * Muestra el detalle
     */
    public function show(AgendaDesplazamiento $agenda)
    {
        $agenda->load(['actividades', 'estado']);
        $user = auth()->user();

        // --- VALIDACIÓN DE SEGURIDAD (CANDADO) ---
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
        $funcionarioId = $funcionario ? $funcionario->id : null;

        $isAuthorized = ($user->role === 'administrador') ||
                        ($user->role === 'viaticos') ||
                        ($agenda->user_id === $user->id) ||
                        ($funcionarioId && ($agenda->supervisor_id === $funcionarioId || $agenda->ordenador_id === $funcionarioId));

        if (!$isAuthorized) {
            abort(403, 'No tiene permisos para ver esta agenda.');
        }
        // -----------------------------------------

        // CASO 1: Si requiere corrección
        if ($agenda->estado->nombre == 'ENVIADA' && $agenda->observaciones_finanzas) {
            return redirect()->route('formulario', ['agenda_id' => $agenda->id]);
        }

        // Si es viáticos, lo mandamos a la pantalla de gestión
        if ($user->role == 'viaticos') {
            return view('viaticos.gestionar', compact('agenda'));
        }

        if ($user->role == 'supervisor_contrato') {
            return redirect()->route('reportes')->with('info', 'Usted no tiene permisos para reportar actividades.');
        }

        // Proxima fecha
        $ultimaActividad = $agenda->actividades->sortByDesc('fecha')->first();
        $proximaFecha = $agenda->fecha_inicio->format('Y-m-d');

        if ($ultimaActividad) {
            $proximaFecha = \Carbon\Carbon::parse($ultimaActividad->fecha)->addDay()->format('Y-m-d');
        }

        return view('reportar_dia.show', compact('agenda', 'proximaFecha'));
    }

    /**
     * Guarda actividades
     */
    public function store(Request $request, AgendaDesplazamiento $agenda)
    {
        // --- VALIDACIÓN DE SEGURIDAD (Solo el dueño puede reportar) ---
        if ($agenda->user_id !== auth()->id() && auth()->user()->role !== 'administrador') {
            abort(403, 'No tiene permisos para modificar esta agenda.');
        }

        $request->validate([
            'fecha' => 'required|date',
            'ruta_ida' => 'required|string',
            'ruta_regreso' => 'required|string',
            'actividad' => 'required|string',
            'transporte_ida' => 'nullable|array',
            'transporte_regreso' => 'nullable|array',
        ]);

        $agenda->actividades()->create([
            'fecha' => $request->fecha,
            'ruta_ida' => $request->ruta_ida,
            'ruta_regreso' => $request->ruta_regreso,
            'transporte_ida' => $request->transporte_ida,
            'transporte_regreso' => $request->transporte_regreso,
            'actividad' => $request->actividad,
        ]);

        return redirect()
            ->route('reportes.show', $agenda->id)
            ->with('success', 'Actividad registrada correctamente');
    }

    /**
     * Elimina una agenda (Solo Supervisor o Administrador)
     */
    public function destroy(AgendaDesplazamiento $agenda)
    {
        $user = auth()->user();
        
        // --- VALIDACIÓN DE SEGURIDAD ---
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
        $funcionarioId = $funcionario ? $funcionario->id : null;

        $isAuthorized = ($user->role === 'administrador') ||
                        ($user->role === 'viaticos' && $agenda->estado->nombre === 'BORRADOR') ||
                        ($funcionarioId && ($agenda->supervisor_id === $funcionarioId || $agenda->ordenador_id === $funcionarioId));

        if (!$isAuthorized) {
            abort(403, 'No tiene permisos para eliminar esta agenda.');
        }
        // --------------------------------

        // Eliminar registros relacionados (por si no están en cascada en la BD)
        $agenda->actividades()->delete();
        $agenda->obligaciones()->detach();
        
        // Eliminar la legalización asociada si existe
        if ($agenda->legalizacion) {
            $agenda->legalizacion->delete();
        }

        // Eliminar PDFs finales si existen (soportando formato antiguo y nuevo con cédula)
        $doc = $agenda->user->numero_documento ?? '';
        $pathsToDelete = [
            storage_path('app/final_pdfs/agenda_' . $agenda->id . '.pdf'),
            storage_path('app/final_pdfs/agenda_' . $agenda->id . '_' . $doc . '.pdf'),
            storage_path('app/final_pdfs/legalizacion_' . $agenda->id . '.pdf'),
            storage_path('app/final_pdfs/legalizacion_' . $agenda->id . '_' . $doc . '.pdf'),
        ];
        foreach ($pathsToDelete as $p) {
            if (file_exists($p)) {
                @unlink($p);
            }
        }
        
        // Eliminar la agenda
        $agenda->delete();

        return back()->with('success', 'Agenda eliminada permanentemente del sistema.');
    }
}