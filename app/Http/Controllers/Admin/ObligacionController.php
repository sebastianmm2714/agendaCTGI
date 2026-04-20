<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ObligacionContrato;
use App\Models\CategoriaPersonal;
use Illuminate\Http\Request;

class ObligacionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 10);

        $query = ObligacionContrato::with('categoria');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                  ->orWhereHas('categoria', function($cq) use ($search) {
                      $cq->where('nombre', 'like', "%$search%");
                  });
            });
        }

        $obligaciones = $query->paginate($perPage)->appends($request->all());
        $categorias = CategoriaPersonal::all();
        
        return view('admin.obligaciones.index', compact('obligaciones', 'categorias'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'categoria_personal_id' => 'required|exists:categorias_personal,id',
        ]);

        ObligacionContrato::create($request->all());

        return back()->with('success', 'Obligación creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string',
            'categoria_personal_id' => 'required|exists:categorias_personal,id',
        ]);

        $obligacion = ObligacionContrato::findOrFail($id);
        $obligacion->update($request->all());

        return back()->with('success', 'Obligación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $obligacion = ObligacionContrato::findOrFail($id);
        
        // Verificar si tiene agendas asociadas
        if ($obligacion->agendas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una obligación que ya ha sido usada en agendas.');
        }

        $obligacion->delete();

        return back()->with('success', 'Obligación eliminada correctamente.');
    }
}
