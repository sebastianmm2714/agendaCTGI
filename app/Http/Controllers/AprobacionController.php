<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;
use Illuminate\Support\Facades\Storage;

class AprobacionController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('inicio');
    }

    public function autorizar(Request $request, $id)
    {
        $agenda = AgendaDesplazamiento::with('user')->findOrFail($id);
        $user = auth()->user();
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();

        if (!$funcionario || $agenda->supervisor_id !== $funcionario->id) {
            abort(403, 'No tiene permisos para autorizar esta agenda.');
        }

        $estadoAprobado = \App\Models\EstadoAgenda::where('nombre', 'APROBADA_SUPERVISOR')->first();
        
        $esFuncionarioAgenda = ($agenda->user && $agenda->user->role === 'funcionario');

        // Solo exigir firma si NO es una agenda de funcionario
        if (!$esFuncionarioAgenda) {
            if (!$user->firma || !$funcionario || !$funcionario->firma) {
                return redirect()->back()->withErrors(['firma' => 'Debe registrar su firma digital en la sección "Mi Firma" antes de autorizar agendas de contratistas.']);
            }
        }

        // Limpieza de firma anterior si existe en la agenda
        if (!$esFuncionarioAgenda && $agenda->supervisor_id) {
            \App\Models\LiderDeProceso::where('id', $agenda->supervisor_id)->update(['firma' => $user->firma]);
        }

        $agenda->update([
            'estado_id' => $estadoAprobado->id,
            'firma_supervisor_path' => $esFuncionarioAgenda ? null : ($funcionario->firma ?? null),
            'observaciones_finanzas' => null 
        ]);

        return redirect()->to(session('back_url_reportar_dia', route('inicio')))
            ->with('success', 'Agenda autorizada y enviada a Viáticos.');
    }

    public function devolver(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        $agenda = AgendaDesplazamiento::findOrFail($id);
        $user = auth()->user();
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();

        if (!$funcionario || $agenda->supervisor_id !== $funcionario->id) {
            abort(403, 'No tiene permisos para devolver esta agenda.');
        }

        $estadoRetornado = \App\Models\EstadoAgenda::where('nombre', 'CORRECCIÓN')->first();

        $agenda->update([
            'estado_id' => $estadoRetornado->id,
            'observaciones_finanzas' => $request->observaciones, // Reutilizando este campo para el feedback
        ]);

        return redirect()->to(session('back_url_reportar_dia', route('inicio')))
            ->with('success', 'La agenda ha sido devuelta al contratista para su corrección.');
    }
}