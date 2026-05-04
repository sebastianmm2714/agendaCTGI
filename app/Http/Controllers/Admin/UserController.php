<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CategoriaPersonal;
use App\Models\LiderDeProceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 10);

        $query = User::with(['categoria', 'supervisor', 'ordenador'])
            ->orderBy('name');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('numero_documento', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->paginate($perPage)->appends($request->all());
            
        $categorias = CategoriaPersonal::orderBy('nombre')->get();
        $supervisores = LiderDeProceso::where('tipo', 'SUPERVISOR')->orderBy('nombre')->get();
        $ordenadores = LiderDeProceso::where('tipo', 'ORDENADOR')->orderBy('nombre')->get();

        return view('admin.usuarios.index', compact('users', 'categorias', 'supervisores', 'ordenadores'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'numero_documento' => 'required|string|unique:users,numero_documento',
            'tipo_documento' => 'required|string|max:20',
            'salario_honorarios' => 'nullable|numeric|min:0',
            'numero_cuenta_tipo' => 'nullable|string|max:100',
            'role' => 'required|in:contratista,administrador,viaticos,supervisor_contrato,ordenador_gasto',
            'categoria_personal_id' => 'required|exists:categorias_personal,id',
            'numero_contrato' => 'required|string|max:100',
            'anio_contrato' => 'required|numeric|digits:4',
            'fecha_vencimiento' => 'required|date',
            'objeto_contractual' => 'required|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'salario_honorarios' => $request->salario_honorarios,
            'numero_cuenta_tipo' => $request->numero_cuenta_tipo,
            'password' => Hash::make($request->numero_documento),
            'role' => $request->role,
            'categoria_personal_id' => $request->categoria_personal_id,
            'supervisor_id' => $request->supervisor_id,
            'ordenador_id' => $request->ordenador_id,
            'numero_contrato' => $request->numero_contrato,
            'anio_contrato' => $request->anio_contrato,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'objeto_contractual' => $request->objeto_contractual,
        ]);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'numero_documento' => 'required|string|unique:users,numero_documento,' . $usuario->id,
            'tipo_documento' => 'required|string|max:20',
            'salario_honorarios' => 'nullable|numeric|min:0',
            'numero_cuenta_tipo' => 'nullable|string|max:100',
            'role' => 'required|in:contratista,administrador,viaticos,supervisor_contrato,ordenador_gasto',
            'categoria_personal_id' => 'required|exists:categorias_personal,id',
            'numero_contrato' => 'required|string|max:100',
            'anio_contrato' => 'required|numeric|digits:4',
            'fecha_vencimiento' => 'required|date',
            'objeto_contractual' => 'required|string',
        ]);

        // Actualizar usuario. El UserObserver se encargará de sincronizar con 'lideres_de_proceso'.
        $usuario->update($request->all());

        return back()->with('success', 'Usuario actualizado correctamente en ambas tablas.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->agendas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un usuario que tiene agendas registradas.');
        }

        $usuario->delete();

        return back()->with('success', 'Usuario eliminado correctamente.');
    }

    public function checkDocument(Request $request)
    {
        $exists = User::where('numero_documento', $request->documento)->exists();
        return response()->json(['exists' => $exists]);
    }
}
