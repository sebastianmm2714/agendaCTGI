<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;

class ViaticosController extends Controller
{
    public function index()
    {
        $agendas = AgendaDesplazamiento::where('estado', 'VIATICOS')->get();
        return view('viaticos.index', compact('agendas'));
    }

    public function aprobar(Request $request, $id)
    {
        $agenda = AgendaDesplazamiento::findOrFail($id);

        // El rol de viáticos solo revisa, no firma digitalmente el PDF en este paso
        // simplemente lo pasa al ordenador de gasto.
        $agenda->update(['estado' => 'REVISION']);

        return redirect()->route('inicio')->with('success', 'Agenda revisada por Viáticos y enviada al Ordenador de Gasto.');
    }
}
