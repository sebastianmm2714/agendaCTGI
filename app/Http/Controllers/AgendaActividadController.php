<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaActividad;
use App\Models\AgendaDesplazamiento;
use Carbon\Carbon;

class AgendaActividadController extends Controller
{
    public function store(Request $request, $agendaId)
    {
        $agenda = AgendaDesplazamiento::findOrFail($agendaId);

        $data = $request->validate([
            'fecha_reporte' => 'required|date',
            'transporte_ida' => 'required|array|min:1',
            'transporte_ida.*' => 'in:aereo,terrestre,fluvial',
            'transporte_regreso' => 'required|array|min:1',
            'transporte_regreso.*' => 'in:aereo,terrestre,fluvial',

            'actividades' => 'required|array|min:1',
            'actividades.*.hora' => 'required|string',
            'actividades.*.actividad' => 'required|string|max:160',
            'valor_aereo' => 'nullable|string|max:50',
            'valor_terrestre' => 'nullable|string|max:50',
            'valor_intermunicipal' => 'nullable|string|max:50',
        ]);

        $actividad = AgendaActividad::create([
            'agenda_desplazamiento_id' => $agenda->id,
            'fecha_reporte' => $data['fecha_reporte'],
            'ruta_ida' => 'MEDELLIN - ' . $agenda->ciudad_destino,
            'ruta_regreso' => $agenda->ciudad_destino . ' - MEDELLIN',
            'transporte_ida' => $data['transporte_ida'],
            'transporte_regreso' => $data['transporte_regreso'],
            'medios_transporte' => $data['transporte_ida'], // Fallback for old code if any
            'actividades_ejecutar' => $data['actividades'],
            'valor_aereo' => $data['valor_aereo'] ?? null,
            'valor_terrestre' => $data['valor_terrestre'] ?? null,
            'valor_intermunicipal' => $data['valor_intermunicipal'] ?? null,
        ]);

        return back()->with('success', 'Actividad registrada correctamente');
    }
}
