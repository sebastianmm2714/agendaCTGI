<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function index()
    {
        $agendas = \App\Models\AgendaDesplazamiento::with('user')->get();
        return view('reportes.index', compact('agendas'));
    }

    public function show(\App\Models\AgendaDesplazamiento $agenda)
    {
        return view('reportes.show', compact('agenda'));
    }
}
