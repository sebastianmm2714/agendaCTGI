<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SubdirectorController extends Controller
{
    /**
     * Muestra la bandeja de entrada del Subdirector.
     * Ahora el filtro cambia: Solo ve agendas con estado 'APROBADA_VIATICOS' y 'APROBADA'
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();

        if (!$funcionario) {
            return view('subdirector.index', [
                'pendientes' => collect([]),
                'aprobadas'  => collect([]),
                'devueltas'  => collect([]),
                'activeTab'  => 'pendientes',
            ]);
        }

        $baseQuery = AgendaDesplazamiento::where('ordenador_id', $funcionario->id)
            ->whereHas('estado', function($q) {
                $q->whereIn('nombre', ['APROBADA_VIATICOS', 'APROBADA', 'CORRECCI\u00d3N']);
            })
            ->with(['user', 'estado', 'user.categoria'])
            ->orderBy('updated_at', 'desc');

        // BÚSQUEDA INTELIGENTE
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function($q) use ($search) {
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

        $activeTab = $request->get('tab', 'pendientes');
        $perPage   = (int) $request->get('per_page', 5);

        $pendientes = (clone $baseQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'APROBADA_VIATICOS');
        })->paginate($perPage, ['*'], 'page_p')->appends($request->except('page_p'));

        $aprobadas = (clone $baseQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'APROBADA');
        })->paginate($perPage, ['*'], 'page_a')->appends($request->except('page_a'));

        $devueltas = (clone $baseQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'CORRECCI\u00d3N');
        })->paginate($perPage, ['*'], 'page_d')->appends($request->except('page_d'));

        return view('subdirector.index', compact('pendientes', 'aprobadas', 'devueltas', 'activeTab'));
    }

    /**
     * Proceso de firma del Subdirector (Ordenador de Gasto)
     */
    public function autorizar(Request $request, $id)
    {
        // 1. Validación de seguridad
        if (Auth::user()->role !== 'ordenador_gasto') {
            return redirect()->back()->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $user = Auth::user();

        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();

        if (!$user->firma || !$funcionario || !$funcionario->firma) {
            return redirect()->back()->withErrors(['firma' => 'Debe registrar su firma digital en la sección "Mi Firma" antes de autorizar agendas.']);
        }

        // 2. Localizar la agenda
        $agenda = AgendaDesplazamiento::findOrFail($id);
        $estadoAprobada = \App\Models\EstadoAgenda::where('nombre', 'APROBADA')->first();

        // 3. Procesar la firma y actualizar estado
        if ($agenda->ordenador_id) {
            \App\Models\LiderDeProceso::where('id', $agenda->ordenador_id)->update(['firma' => $user->firma]);
        }

        $agenda->update([
            'estado_id' => $estadoAprobada->id,
            'firma_ordenador_path' => $funcionario->firma,
        ]);

        return redirect()->route('ordenador_gasto.index')->with('success', 'Agenda autorizada y firmada correctamente. El proceso ha finalizado.');
    }
}