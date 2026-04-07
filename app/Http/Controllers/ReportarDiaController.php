<?php

namespace App\Http\Controllers;

use App\Models\AgendaDesplazamiento;
use Illuminate\Http\Request;

class ReportarDiaController extends Controller
{
    /**
     * Muestra la lista de agendas del usuario para reportar días.
     */
    public function index()
    {
        $agendas = AgendaDesplazamiento::with('estado')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('reportar_dia.index', compact('agendas'));
    }

    /**
     * Muestra el formulario para reportar un día específico en una agenda.
     */
    public function show(AgendaDesplazamiento $agenda)
    {
        // Validar que la agenda pertenezca al usuario (A menos que sea administrador)
        if ($agenda->user_id !== auth()->id() && auth()->user()->role !== 'administrador') {
            abort(403, 'No tienes permiso para acceder a esta agenda.');
        }

        $agenda->load('actividades');

        // Encontrar la última fecha reportada
        $ultimaActividad = $agenda->actividades->sortByDesc('fecha')->first();

        $proximaFecha = $agenda->fecha_inicio->format('Y-m-d');

        if ($ultimaActividad) {
            // Si hay actividades, la próxima fecha es el día siguiente a la última reportada
            $proximaFecha = \Carbon\Carbon::parse($ultimaActividad->fecha)->addDay()->format('Y-m-d');
        }

        return view('reportar_dia.show', compact('agenda', 'proximaFecha'));
    }

    /**
     * Guarda la actividad reportada para un día.
     */
    public function store(Request $request, AgendaDesplazamiento $agenda)
    {
        // Validar propiedad (A menos que sea administrador)
        if ($agenda->user_id !== auth()->id() && auth()->user()->role !== 'administrador') {
            abort(403);
        }

        // Limpiar puntos de los valores monetarios
        $fieldsToClean = ['valor_aereo', 'valor_terrestre', 'valor_intermunicipal'];
        foreach ($fieldsToClean as $field) {
            if ($request->has($field)) {
                $value = str_replace('.', '', $request->get($field));
                $request->merge([$field => $value === '' ? 0 : $value]);
            }
        }

        // Validaciones
        $request->validate([
            'fecha' => [
                'required',
                'date',
                'after_or_equal:' . $agenda->fecha_inicio->format('Y-m-d'),
                'before_or_equal:' . $agenda->fecha_fin->format('Y-m-d'),
            ],
            'transporte_ida' => ($agenda->actividades->count() == 0 ? 'required' : 'nullable') . '|array',
            'transporte_regreso' => ($agenda->actividades->count() == 0 ? 'required' : 'nullable') . '|array',
            'actividades' => 'required|array',
            'valor_aereo' => 'nullable|numeric|min:0',
            'valor_terrestre' => 'nullable|numeric|min:0',
            'valor_intermunicipal' => 'nullable|numeric|min:0',
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no tiene un formato válido.',
            'fecha.after_or_equal' => 'Solo puede reportar días en el lapso de los días hábiles del desplazamiento (desde ' . $agenda->fecha_inicio->format('d/m/Y') . ').',
            'fecha.before_or_equal' => 'Solo puede reportar días en el lapso de los días hábiles del desplazamiento (hasta ' . $agenda->fecha_fin->format('d/m/Y') . ').',
            'actividades.required' => 'Debe agregar al menos una actividad.',
        ]);

        // Validar solapamientos de horas en el reporte
        $ranges = [];
        foreach ($request->actividades as $index => $act) {
            $hora = $act['hora'] ?? '';
            if (empty($hora)) continue;

            $parts = explode('-', $hora);
            if (count($parts) !== 2) continue;

            $start = $this->timeToMinutes(trim($parts[0]));
            $end = $this->timeToMinutes(trim($parts[1]));

            if ($start === null || $end === null || $start >= $end) {
                return back()->withInput()->withErrors(["actividades.$index.hora" => 'Horario inválido.']);
            }

            // Comparar contra rangos ya procesados
            foreach ($ranges as $r) {
                if ($start < $r['end'] && $end > $r['start']) {
                    return back()->withInput()->withErrors(['actividades' => "Se han detectado horarios que se cruzan: $hora"]);
                }
            }
            $ranges[] = ['start' => $start, 'end' => $end];
        }

        // Crear la actividad
        $agenda->actividades()->create([
            'fecha' => $request->fecha,
            'ruta_ida' => $request->ruta_ida,
            'ruta_regreso' => $request->ruta_regreso,
            'transporte_ida' => $request->transporte_ida,
            'transporte_regreso' => $request->transporte_regreso,
            'actividad' => $request->actividades,
            'valor_aereo' => $request->valor_aereo ?? 0,
            'valor_terrestre' => $request->valor_terrestre ?? 0,
            'valor_intermunicipal' => $request->valor_intermunicipal ?? 0,
        ]);

        return redirect()->route(auth()->user()->role == 'administrador' ? 'reportes.show' : 'reportar-dia.show', $agenda->id)
            ->with('success', 'Día reportado correctamente.');
    }

    /**
     * Envía la agenda al supervisor.
     */
    public function enviar(AgendaDesplazamiento $agenda)
    {
        // Validar propiedad
        if (!in_array($agenda->estado->nombre, ['BORRADOR', 'CORRECCIÓN'])) {
            return back()->with('error', 'Esta agenda ya ha sido enviada o procesada.');
        }

        // VALIDACIÓN: Todos los días deben estar reportados
        $diasEsperados = $agenda->fecha_inicio->diffInDays($agenda->fecha_fin) + 1;
        $diasReportados = $agenda->actividades()->count();

        if ($diasReportados < $diasEsperados) {
            return redirect()->back()->with('error', "No puedes enviar la agenda aún. Debes reportar actividades para los {$diasEsperados} días del desplazamiento.");
        }

        // Actualizar estado a ENVIADA (Asumiendo que ENVIADA es un estado válido en catalogos)
        // O mejor buscar el ID del estado ENVIADA
        $estadoEnviada = \App\Models\EstadoAgenda::where('nombre', 'ENVIADA')->first();
        
        $agenda->update([
            'estado_id' => $estadoEnviada->id,
            'observaciones_finanzas' => null 
        ]);

        return redirect()->route('reportar-dia')
            ->with('success', 'Agenda enviada correctamente al supervisor.');
    }

    /**
     * Convierte un string de hora (08:00 AM) a minutos desde la medianoche.
     */
    private function timeToMinutes($timeStr)
    {
        if (empty($timeStr)) return null;
        if (!preg_match('/(\d+):(\d+)\s*(AM|PM)/i', $timeStr, $matches)) return null;

        $hours = (int)$matches[1];
        $minutes = (int)$matches[2];
        $ampm = strtoupper($matches[3]);

        if ($ampm === 'PM' && $hours < 12) $hours += 12;
        if ($ampm === 'AM' && $hours === 12) $hours = 0;

        return $hours * 60 + $minutes;
    }
}
