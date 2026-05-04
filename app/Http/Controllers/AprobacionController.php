<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;
use Illuminate\Support\Facades\Storage;

class AprobacionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();

        if (!$funcionario) {
            return view('coordinador.index', [
                'pendientes' => collect([]), 
                'enviadas' => collect([]), 
                'devueltas' => collect([])
            ]);
        }

        $query = AgendaDesplazamiento::where('supervisor_id', $funcionario->id)
            ->with(['user', 'estado', 'user.categoria'])
            ->orderBy('updated_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    $qu->where('name', 'like', "%$search%")
                       ->orWhere('numero_documento', 'like', "%$search%");
                })
                ->orWhereHas('user.categoria', function($qc) use ($search) {
                    $qc->where('nombre', 'like', "%$search%");
                })
                ->orWhere('ruta', 'like', "%$search%")
                ->orWhere('destinos', 'like', "%$search%")
                ->orWhereRaw("DATE_FORMAT(fecha_inicio, '%d/%m/%Y') LIKE ?", ["%$search%"])
                ->orWhereRaw("DATE_FORMAT(fecha_fin, '%d/%m/%Y') LIKE ?", ["%$search%"]);
            });
        }

        // Tab activo y registros por página
        $activeTab = $request->get('tab', 'pendientes');
        $perPage   = (int) $request->get('per_page', 5);

        // Paginado independiente para conservar estado entre cambios de página
        $pendientes = (clone $query)->whereHas('estado', function($q) {
            $q->where('nombre', 'ENVIADA');
        })->paginate($perPage, ['*'], 'page_p')->appends($request->except('page_p'));

        $enviadas = (clone $query)->whereHas('estado', function($q) {
            $q->whereIn('nombre', ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA']);
        })->paginate($perPage, ['*'], 'page_e')->appends($request->except('page_e'));

        $devueltas = (clone $query)->whereHas('estado', function($q) {
            $q->where('nombre', 'CORRECCIÓN');
        })->paginate($perPage, ['*'], 'page_d')->appends($request->except('page_d'));

        // Tab activo
        $activeTab = $request->get('tab', 'pendientes');

        return view('coordinador.index', compact('pendientes', 'enviadas', 'devueltas', 'activeTab'));
    }

    public function autorizar(Request $request, $id)
    {
        $agenda = AgendaDesplazamiento::findOrFail($id);
        $estadoAprobado = \App\Models\EstadoAgenda::where('nombre', 'APROBADA_SUPERVISOR')->first();
        $user = auth()->user();

        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();
        
        if (!$user->firma || !$funcionario || !$funcionario->firma) {
            return redirect()->back()->withErrors(['firma' => 'Debe registrar su firma digital en la sección "Mi Firma" antes de autorizar agendas.']);
        }

        // Limpieza de firma anterior si existe en la agenda, aunque ahora usaremos la misma ruta del perfil
        // No es estrictamente necesario eliminarla del disco aquí si es la misma del perfil, 
        // pero podemos simplemente actualizar el registro.

        if ($agenda->supervisor_id) {
            \App\Models\LiderDeProceso::where('id', $agenda->supervisor_id)->update(['firma' => $user->firma]);
        }

        $agenda->update([
            'estado_id' => $estadoAprobado->id,
            'firma_supervisor_path' => $funcionario->firma,
            'observaciones_finanzas' => null 
        ]);

        return redirect()->route('supervisor_contrato.index')
            ->with('alerta_exitosa', 'Agenda autorizada y enviada a Viáticos.');
    }

    public function devolver(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        $agenda = AgendaDesplazamiento::findOrFail($id);
        $estadoRetornado = \App\Models\EstadoAgenda::where('nombre', 'CORRECCIÓN')->first();

        $agenda->update([
            'estado_id' => $estadoRetornado->id,
            'observaciones_finanzas' => $request->observaciones, // Reutilizando este campo para el feedback
        ]);

        return redirect()->route('supervisor_contrato.index')
            ->with('alerta_exitosa', 'La agenda ha sido devuelta al contratista para su corrección.');
    }
}