<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AprobacionController extends Controller
{
    public function index()
    {
        $agendas = \App\Models\AgendaDesplazamiento::where('estado', 'ENVIADA')->get();
        return view('coordinador.index', compact('agendas'));
    }

    public function autorizar(Request $request, $id)
    {
        $agenda = \App\Models\AgendaDesplazamiento::findOrFail($id);

        $data = ['estado' => 'VIATICOS'];

        if ($request->hasFile('firma_archivo')) {
            $data['firma_supervisor'] = $request->file('firma_archivo')->store('firmas', 'public');
        }

        $agenda->update($data);

        return back()->with('success', 'Agenda autorizada por el supervisor del contrato.');
    }
}
