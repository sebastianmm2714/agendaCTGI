<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Conteos basados en el flujo: BORRADOR -> ENVIADA -> VIATICOS -> REVISION -> APROBADA
        $pendientesCoord = \App\Models\AgendaDesplazamiento::where('estado', 'ENVIADA')->count();
        $pendientesViaticos = \App\Models\AgendaDesplazamiento::where('estado', 'VIATICOS')->count();
        $pendientesSub = \App\Models\AgendaDesplazamiento::where('estado', 'REVISION')->count();

        return view('inicio', compact('pendientesCoord', 'pendientesViaticos', 'pendientesSub'));
    }
}
