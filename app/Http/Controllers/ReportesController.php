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
    public function index()
    {
        $user = auth()->user();

        // CASO 1: Si el usuario es de VIÁTICOS
        if ($user->role == 'viaticos') {
            return redirect()->route('viaticos.index');
        }

        // CASO 2: Para los demás (Contratistas, etc.)
        $query = AgendaDesplazamiento::with(['user', 'estado', 'actividades']);

        if ($user->role == 'contratista') {
            $query->where('user_id', $user->id);
        }
        elseif ($user->role == 'supervisor_contrato') {
            $funcionario = \App\Models\Funcionario::where('numero_documento', $user->numero_documento)->first();
            if (!$funcionario) return view('reportes.index', ['agendas' => collect()]);

            $query->where('supervisor_id', $funcionario->id)
                  ->where(function($q) {
                $q->whereHas('estado', function($qe) {
                    $qe->whereIn('nombre', ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA', 'CORRECCIÓN']);
                })
                ->orWhere(function($qo) {
                    $qo->whereHas('estado', function($qe) {
                        $qe->where('nombre', 'ENVIADA');
                    })->whereNotNull('observaciones_finanzas');
                });
            });
        }
        elseif ($user->role == 'ordenador_gasto') {
            $funcionario = \App\Models\Funcionario::where('numero_documento', $user->numero_documento)->first();
            if (!$funcionario) return view('reportes.index', ['agendas' => collect()]);

            $query->where('ordenador_id', $funcionario->id)
                  ->whereHas('estado', function($q) {
                $q->whereIn('nombre', ['APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA']);
            });
        }

        $agendas = $query->orderBy('created_at', 'desc')->get();

        return view('reportes.index', compact('agendas'));
    }

    /**
     * Muestra el detalle
     */
    public function show(AgendaDesplazamiento $agenda)
    {
        $agenda->load(['actividades', 'estado']);
        $user = auth()->user();

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
}