<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;
use Illuminate\Support\Facades\Storage;

class AprobacionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Buscar el registro de funcionario que corresponde a este usuario
        $funcionario = \App\Models\Funcionario::where('numero_documento', $user->numero_documento)->first();

        // Si no es funcionario, no debería ver nada (seguridad extra)
        if (!$funcionario) {
            return view('coordinador.index', ['agendas' => collect()]);
        }

        $agendas = AgendaDesplazamiento::where('supervisor_id', $funcionario->id)
            ->whereHas('estado', function($q) {
                $q->whereIn('nombre', ['ENVIADA', 'APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA', 'CORRECCIÓN']);
            })
            ->with(['user', 'estado', 'user.categoria'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('coordinador.index', compact('agendas'));
    }

    public function autorizar(Request $request, $id)
    {
        $agenda = AgendaDesplazamiento::findOrFail($id);
        $estadoAprobado = \App\Models\EstadoAgenda::where('nombre', 'APROBADA_SUPERVISOR')->first();
        $user = auth()->user();

        if (!$user->firma) {
            return redirect()->back()->withErrors(['firma' => 'Debe registrar su firma digital en la sección "Mi Firma" antes de autorizar agendas.']);
        }

        // Limpieza de firma anterior si existe en la agenda, aunque ahora usaremos la misma ruta del perfil
        // No es estrictamente necesario eliminarla del disco aquí si es la misma del perfil, 
        // pero podemos simplemente actualizar el registro.

        if ($agenda->supervisor_id) {
            \App\Models\Funcionario::where('id', $agenda->supervisor_id)->update(['firma' => $user->firma]);
        }

        $agenda->update([
            'estado_id' => $estadoAprobado->id,
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