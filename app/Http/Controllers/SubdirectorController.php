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
    public function index()
    {
        $user = auth()->user();
        
        // Buscar el registro de funcionario que corresponde a este usuario
        $funcionario = \App\Models\Funcionario::where('numero_documento', $user->numero_documento)->first();

        // Si no es funcionario, no debería ver nada (seguridad extra)
        if (!$funcionario) {
            return view('subdirector.index', ['agendas' => collect()]);
        }

        $agendas = AgendaDesplazamiento::where('ordenador_id', $funcionario->id)
            ->whereHas('estado', function($q) {
                $q->whereIn('nombre', ['APROBADA_VIATICOS', 'APROBADA', 'CORRECCIÓN']);
            })
            ->with(['user', 'estado'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('subdirector.index', compact('agendas'));
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

        if (!$user->firma) {
            return redirect()->back()->withErrors(['firma' => 'Debe registrar su firma digital en la sección "Mi Firma" antes de autorizar agendas.']);
        }

        // 2. Localizar la agenda
        $agenda = AgendaDesplazamiento::findOrFail($id);
        $estadoAprobada = \App\Models\EstadoAgenda::where('nombre', 'APROBADA')->first();

        // 3. Procesar la firma y actualizar estado
        if ($agenda->ordenador_id) {
            \App\Models\Funcionario::where('id', $agenda->ordenador_id)->update(['firma' => $user->firma]);
        }

        $agenda->update([
            'estado_id' => $estadoAprobada->id,
        ]);

        return redirect()->route('ordenador_gasto.index')->with('success', 'Agenda autorizada y firmada correctamente. El proceso ha finalizado.');
    }
}