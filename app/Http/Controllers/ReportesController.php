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
            return redirect()->route('viaticos.index');
        }

        $activeTab = $request->get('tab', 'pendientes');
        $perPage   = (int) $request->get('per_page', 6);
        $search    = $request->get('search');

        $query = AgendaDesplazamiento::with(['user.categoria', 'estado', 'actividades'])
            ->orderBy('updated_at', 'desc');

        // Búsqueda inteligente
        if ($request->filled('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    $qu->where('name', 'like', "%$search%")
                       ->orWhere('numero_documento', 'like', "%$search%");
                })
                ->orWhere('ruta', 'like', "%$search%")
                ->orWhere('destinos', 'like', "%$search%")
                ->orWhereRaw("DATE_FORMAT(fecha_inicio, '%d/%m/%Y') LIKE ?", ["%$search%"])
                ->orWhereRaw("DATE_FORMAT(fecha_fin, '%d/%m/%Y') LIKE ?", ["%$search%"]);
            });
        }

        // Lógica por Rol
        if ($user->role == 'contratista') {
            $query->where('user_id', $user->id);
            
            // Pestañas para Contratista
            $pendientesQuery = (clone $query)->whereHas('estado', function($q) {
                $q->whereIn('nombre', ['BORRADOR', 'ENVIADA'])->whereNull('observaciones_finanzas');
            });
            
            $aprobadasQuery = (clone $query)->whereHas('estado', function($q) {
                $q->whereIn('nombre', ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA']);
            });

            $devueltasQuery = (clone $query)->where(function($q) {
                $q->whereHas('estado', function($qe) {
                    $qe->where('nombre', 'CORRECCI\u00d3N');
                })->orWhere(function($qo) {
                    $qo->whereHas('estado', function($qe) {
                        $qe->where('nombre', 'ENVIADA');
                    })->whereNotNull('observaciones_finanzas');
                });
            });

            $pendientes = $pendientesQuery->paginate($perPage, ['*'], 'page_p')->appends($request->all());
            $aprobadas  = $aprobadasQuery->paginate($perPage, ['*'], 'page_a')->appends($request->all());
            $devueltas  = $devueltasQuery->paginate($perPage, ['*'], 'page_d')->appends($request->all());
            
            session(['back_url_reportar_dia' => $request->fullUrl()]);

            return view('reportes.index', compact('pendientes', 'aprobadas', 'devueltas', 'activeTab'));

        } elseif ($user->role == 'supervisor_contrato') {
            $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
            if (!$funcionario) return view('reportes.index', ['agendas' => collect(), 'estados' => collect()]);

            $query->where('supervisor_id', $funcionario->id);
        } elseif ($user->role == 'ordenador_gasto') {
            $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
            if (!$funcionario) return view('reportes.index', ['agendas' => collect(), 'estados' => collect()]);

            $query->where('ordenador_id', $funcionario->id);
        }

        // Para otros roles (Admin/Supervisor/Ordenador) mantenemos la vista clásica de tabla única por ahora o adaptamos
        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        $agendas = $query->paginate($perPage)->appends($request->all());
        session(['back_url_reportar_dia' => $request->fullUrl()]);
        
        $estados = \App\Models\EstadoAgenda::whereNotIn('nombre', ['BORRADOR', 'ENVIADA'])->get();

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
                        ($funcionarioId && ($agenda->supervisor_id === $funcionarioId));

        if (!$isAuthorized) {
            abort(403, 'No tiene permisos para eliminar esta agenda.');
        }
        // --------------------------------

        // Eliminar registros relacionados (por si no están en cascada en la BD)
        $agenda->actividades()->delete();
        $agenda->obligaciones()->detach();
        
        // Eliminar la agenda
        $agenda->delete();

        return redirect()->route('reportes')->with('success', 'Agenda eliminada permanentemente del sistema.');
    }
}