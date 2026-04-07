<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EstadoAgenda;
use Illuminate\Http\Request;

class EstadoAgendaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:estados_agenda,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);

        EstadoAgenda::create($request->all());

        return back()->with('success', 'Estado creado correctamente.');
    }

    public function update(Request $request, EstadoAgenda $estado)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:estados_agenda,nombre,' . $estado->id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $estado->update($request->all());

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(EstadoAgenda $estado)
    {
        if ($estado->agendas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un estado que ya tiene agendas asociadas.');
        }

        $estado->delete();

        return back()->with('success', 'Estado eliminado correctamente.');
    }
}
