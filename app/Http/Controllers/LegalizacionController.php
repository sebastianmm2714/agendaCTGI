<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;
use App\Models\LiderDeProceso;

class LegalizacionController extends Controller
{
    public function index(Request $request)
    {
        $ver = $request->get('ver', 'pendientes');
        
        $baseQuery = AgendaDesplazamiento::with(['user', 'estado', 'legalizacion'])
            ->has('legalizacion');

        // Stats
        $stats = [
            'pendientes' => (clone $baseQuery)->whereHas('legalizacion', function($q) {
                $q->where('estado', 'APROBADA_SUPERVISOR');
            })->count(),
            'aprobadas'  => (clone $baseQuery)->whereHas('legalizacion', function($q) {
                $q->whereIn('estado', ['APROBADA_LEGALIZACION', 'APROBADA_ORDENADOR']);
            })->count(),
            'devueltas'  => (clone $baseQuery)->whereHas('legalizacion', function($q) {
                $q->whereIn('estado', ['DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR']);
            })->count(),
        ];

        // BÚSQUEDA INTELIGENTE
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function($q) use ($search) {
                $searchEscaped = str_replace(['%', '_'], ['\%', '\_'], $search);
                $q->whereHas('user', function($qu) use ($searchEscaped) {
                    $qu->where('name', 'like', "%$searchEscaped%")
                       ->orWhere('numero_documento', 'like', "%$searchEscaped%");
                })
                ->orWhere('ruta', 'like', "%$searchEscaped%")
                ->orWhere('destinos', 'like', "%$searchEscaped%")
                ->orWhere('orden_viaje', 'like', "%$searchEscaped%");
            });
        }

        // Filtrado por estado
        switch ($ver) {
            case 'aprobadas':
                $baseQuery->whereHas('legalizacion', function($q) {
                    $q->whereIn('estado', ['APROBADA_LEGALIZACION', 'APROBADA_ORDENADOR']);
                });
                break;
            case 'devueltas':
                $baseQuery->whereHas('legalizacion', function($q) {
                    $q->whereIn('estado', ['DEVUELTA_SUPERVISOR', 'DEVUELTA_LEGALIZACION', 'DEVUELTA_ORDENADOR']);
                });
                break;
            default:
                $baseQuery->whereHas('legalizacion', function($q) {
                    $q->where('estado', 'APROBADA_SUPERVISOR');
                });
                break;
        }

        $agendas = $baseQuery->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        return view('legalizacion.index', compact('agendas', 'stats', 'ver'));
    }

    public function gestionar(Request $request, $id)
    {
        $agenda = AgendaDesplazamiento::with(['user', 'actividades', 'estado'])->findOrFail($id);
        $tab = $request->get('tab', 'pendientes');
        return view('legalizacion.gestionar', compact('agenda', 'tab'));
    }

    public function procesar(Request $request, $id)
    {
        $agenda = AgendaDesplazamiento::findOrFail($id);

        $request->validate([
            'observaciones' => 'required_if:accion,devolver|string|max:1000|nullable',
            'accion' => 'required|in:aprobar,devolver'
        ]);

        if ($request->accion == 'aprobar') {
            $agenda->update([
                'legalizacion_estado' => 'APROBADA_LEGALIZACION',
                'legalizacion_observaciones' => null
            ]);
            return redirect()->route('inicio', ['ver' => 'enviadas'])->with('success', 'Legalización aprobada correctamente. Enviada al ordenador.');
        }

        if ($request->accion == 'devolver') {
            $agenda->update([
                'legalizacion_estado' => 'DEVUELTA_LEGALIZACION',
                'legalizacion_observaciones' => $request->observaciones
            ]);
            return redirect()->route('inicio', ['ver' => 'devueltas'])->with('warning', 'Legalización devuelta al contratista para su corrección.');
        }

        return redirect()->back()->with('error', 'Acción no reconocida.');
    }
}
