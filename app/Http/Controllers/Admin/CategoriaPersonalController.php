<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaPersonal;
use Illuminate\Http\Request;

class CategoriaPersonalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_personal,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);

        CategoriaPersonal::create($request->all());

        return back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, CategoriaPersonal $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_personal,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $categoria->update($request->all());

        return back()->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(CategoriaPersonal $categoria)
    {
        if ($categoria->usuarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una categoría que tiene usuarios asociados.');
        }

        $categoria->delete();

        return back()->with('success', 'Categoría eliminada correctamente.');
    }
}
