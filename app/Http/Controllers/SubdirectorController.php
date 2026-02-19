<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubdirectorController extends Controller
{
    public function index()
    {
        $agendas = \App\Models\AgendaDesplazamiento::where('estado', 'REVISION')->get();
        return view('subdirector.index', compact('agendas'));
    }

    public function autorizar(Request $request, $id)
    {
        $agenda = \App\Models\AgendaDesplazamiento::findOrFail($id);

        $data = ['estado' => 'APROBADA'];

        if ($request->hasFile('firma_ordenador')) {
            $data['firma_ordenador'] = $request->file('firma_ordenador')->store('firmas', 'public');
        }

        $agenda->update($data);

        return back()->with('success', 'Agenda firmada por el ordenador del gasto.');
    }
}
