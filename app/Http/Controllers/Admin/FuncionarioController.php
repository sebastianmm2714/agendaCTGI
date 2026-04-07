<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funcionario;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{
    public function index()
    {
        $funcionarios = Funcionario::orderBy('nombre')->get();
        return view('admin.funcionarios.index', compact('funcionarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:50|unique:users,numero_documento',
            'cargo' => 'required|string|max:100',
            'tipo' => 'required|in:SUPERVISOR,ORDENADOR,VIATICOS',
            'numero_cuenta_tipo' => 'nullable|string|max:100',
        ]);

        // Mapear tipo de funcionario a rol de usuario
        $role = match($request->tipo) {
            'SUPERVISOR' => 'supervisor_contrato',
            'ORDENADOR' => 'ordenador_gasto',
            'VIATICOS' => 'viaticos',
            default => 'contratista'
        };

        // Crear el usuario primero
        $user = \App\Models\User::create([
            'name' => $request->nombre,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->numero_documento),
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'numero_cuenta_tipo' => $request->numero_cuenta_tipo,
            'role' => $role,
        ]);

        // Crear el funcionario
        Funcionario::create($request->all());

        return back()->with('success', 'Funcionario y cuenta de usuario creados correctamente.');
    }

    public function update(Request $request, Funcionario $funcionario)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:50',
            'cargo' => 'required|string|max:100',
            'tipo' => 'required|in:SUPERVISOR,ORDENADOR,VIATICOS',
            'numero_cuenta_tipo' => 'nullable|string|max:100',
        ]);

        // Actualizar funcionario
        $funcionario->update($request->all());

        // Actualizar usuario vinculado (si existe por email o documento)
        $user = \App\Models\User::where('email', $funcionario->email)
            ->orWhere('numero_documento', $funcionario->numero_documento)
            ->first();

        if ($user) {
            $role = match($request->tipo) {
                'SUPERVISOR' => 'supervisor_contrato',
                'ORDENADOR' => 'ordenador_gasto',
                'VIATICOS' => 'viaticos',
                default => 'contratista'
            };

            $user->update([
                'name' => $request->nombre,
                'email' => $request->email,
                'tipo_documento' => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
                'numero_cuenta_tipo' => $request->numero_cuenta_tipo,
                'role' => $role,
            ]);
        }

        return back()->with('success', 'Funcionario y cuenta vinculada actualizados correctamente.');
    }

    public function destroy(Funcionario $funcionario)
    {
        if ($funcionario->usuariosSupervisor()->count() > 0 || $funcionario->usuariosOrdenador()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un funcionario que tiene usuarios asignados.');
        }

        $funcionario->delete();

        return back()->with('success', 'Funcionario eliminado correctamente.');
    }
}
