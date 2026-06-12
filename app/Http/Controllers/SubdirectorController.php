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
        return redirect()->route('inicio');
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

        if (!$funcionario || $agenda->ordenador_id !== $funcionario->id) {
            abort(403, 'No tiene permisos para autorizar esta agenda.');
        }

        $estadoAprobada = \App\Models\EstadoAgenda::where('nombre', 'APROBADA')->first();

        // 3. Procesar la firma y actualizar estado
        if ($agenda->ordenador_id) {
            \App\Models\LiderDeProceso::where('id', $agenda->ordenador_id)->update(['firma' => $user->firma]);
        }

        $agenda->update([
            'estado_id' => $estadoAprobada->id,
            'firma_ordenador_path' => $funcionario->firma,
        ]);

        return redirect()->to(session('back_url_reportar_dia', route('inicio')))
            ->with('success', 'Agenda autorizada y firmada correctamente. El proceso ha finalizado.');
    }

    public function devolver(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        $agenda = AgendaDesplazamiento::findOrFail($id);
        $user = auth()->user();
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();

        if (!$funcionario || $agenda->ordenador_id !== $funcionario->id) {
            abort(403, 'No tiene permisos para devolver esta agenda.');
        }

        $estadoRetornado = \App\Models\EstadoAgenda::where('nombre', 'CORRECCIÓN')->first();

        $agenda->update([
            'estado_id' => $estadoRetornado->id,
            'observaciones_finanzas' => $request->observaciones,
        ]);

        return redirect()->to(session('back_url_reportar_dia', route('inicio')))
            ->with('success', 'La agenda ha sido devuelta para su corrección.');
    }

    public function autorizarLegalizacion(Request $request, $id)
    {
        if (auth()->user()->role !== 'ordenador_gasto') {
            return redirect()->back()->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $user = auth()->user();
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();

        // Localizar la agenda
        $agenda = AgendaDesplazamiento::findOrFail($id);

        if (!$funcionario || $agenda->ordenador_id !== $funcionario->id) {
            abort(403, 'No tiene permisos para autorizar esta legalización.');
        }

        if ($agenda->legalizacion_estado !== 'APROBADA_LEGALIZACION') {
            return redirect()->back()->with('error', 'La legalización no se encuentra en estado APROBADA_LEGALIZACION.');
        }

        // Si realiza declaración, se exige firma digital del ordenador
        if ($agenda->realiza_declaracion) {
            if (!$user->firma || !$funcionario || !$funcionario->firma) {
                return redirect()->back()->withErrors(['firma' => 'Debe registrar su firma digital en la sección "Mi Firma" antes de autorizar legalizaciones.']);
            }
            
            // Sincronizar firma del ordenador
            if ($agenda->ordenador_id) {
                \App\Models\LiderDeProceso::where('id', $agenda->ordenador_id)->update(['firma' => $user->firma]);
            }
        }

        $agenda->update([
            'legalizacion_estado' => 'APROBADA_ORDENADOR',
            'legalizacion_observaciones' => null,
            'legalizacion_firma_ordenador_path' => $funcionario ? $funcionario->firma : null
        ]);

        $mensajeExito = $agenda->realiza_declaracion 
            ? 'Legalización aprobada y firmada correctamente. El proceso ha finalizado.'
            : 'Legalización aprobada y finalizada correctamente.';

        return redirect()->to(session('back_url_reportar_dia', route('inicio')))
            ->with('success', $mensajeExito);
    }

    public function devolverLegalizacion(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        $agenda = AgendaDesplazamiento::findOrFail($id);
        $user = auth()->user();
        $funcionario = \App\Models\LiderDeProceso::where('numero_documento', $user->numero_documento)->first();

        if (!$funcionario || $agenda->ordenador_id !== $funcionario->id) {
            abort(403, 'No tiene permisos para devolver esta legalización.');
        }

        if ($agenda->legalizacion_estado !== 'APROBADA_LEGALIZACION') {
            return redirect()->back()->with('error', 'La legalización no se encuentra en estado APROBADA_LEGALIZACION.');
        }

        $agenda->update([
            'legalizacion_estado' => 'DEVUELTA_ORDENADOR',
            'legalizacion_observaciones' => $request->observaciones,
        ]);

        return redirect()->to(session('back_url_reportar_dia', route('inicio')))
            ->with('success', 'La legalización ha sido devuelta al contratista para su corrección.');
    }
}