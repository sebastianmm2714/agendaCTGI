<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ViaticosPersonalController extends Controller
{
    /**
     * Muestra la lista de personal (contratistas y líderes de proceso)
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 5);
        $search  = $request->get('search');
        $role    = $request->get('role');
        $vinculacion = $request->get('vinculacion');

        $users = User::where('role', 'contratista')
            ->with(['categoria', 'supervisor', 'ordenador'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%$search%")
                       ->orWhere('numero_documento', 'like', "%$search%")
                       ->orWhere('email', 'like', "%$search%");
                });
            })
            ->when($role, function ($q) use ($role) {
                $q->where('role', $role);
            })
            ->when($vinculacion, function ($q) use ($vinculacion) {
                $q->whereHas('categoria', function ($cq) use ($vinculacion) {
                    $cq->where('nombre', $vinculacion);
                });
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->all());

        $categorias = \App\Models\CategoriaPersonal::orderBy('nombre')->get();
        $supervisores = \App\Models\LiderDeProceso::where('tipo', 'SUPERVISOR')->orderBy('nombre')->get();
        $ordenadores = \App\Models\LiderDeProceso::where('tipo', 'ORDENADOR')->orderBy('nombre')->get();

        return view('viaticos.personal.index', compact('users', 'categorias', 'supervisores', 'ordenadores'));
    }


    /**
     * Almacena un nuevo registro de personal
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'numero_documento'      => 'required|string|max:20|unique:users,numero_documento',
            'tipo_documento'        => 'required|string|max:20',
            'role'                  => 'required|in:contratista,supervisor_contrato,ordenador_gasto,viaticos',
            'categoria_personal_id' => 'required|exists:categorias_personal,id',
            'salario_honorarios'    => 'nullable|numeric|min:0',
            'numero_cuenta_tipo'    => 'nullable|string|max:100',
            'numero_contrato'       => 'required|string|max:100',
            'anio_contrato'         => 'required|numeric|digits:4',
            'fecha_vencimiento'     => 'required|date',
            'objeto_contractual'    => 'required|string',
            'supervisor_id'         => 'nullable|exists:lideres_de_proceso,id',
            'ordenador_id'          => 'nullable|exists:lideres_de_proceso,id',
        ]);

        User::create([
            'name'                  => $request->name,
            'email'                 => $request->email,
            'password'              => Hash::make($request->numero_documento),
            'tipo_documento'        => $request->tipo_documento,
            'numero_documento'      => $request->numero_documento,
            'role'                  => $request->role,
            'categoria_personal_id' => $request->categoria_personal_id,
            'salario_honorarios'    => $request->salario_honorarios,
            'numero_cuenta_tipo'    => $request->numero_cuenta_tipo,
            'numero_contrato'       => $request->numero_contrato,
            'anio_contrato'         => $request->anio_contrato,
            'fecha_vencimiento'     => $request->fecha_vencimiento,
            'objeto_contractual'    => $request->objeto_contractual,
            'supervisor_id'         => $request->supervisor_id,
            'ordenador_id'          => $request->ordenador_id,
        ]);

        return redirect()->route('viaticos.personal.index')
            ->with('success', 'Personal registrado correctamente.');
    }

    /**
     * Actualiza un registro de personal existente
     */
    public function update(Request $request, User $user)
    {
        // Seguridad: Solo permitir editar si el usuario es contratista
        if ($user->role !== 'contratista') {
            return redirect()->route('viaticos.personal.index')
                ->with('error', 'No tiene permisos para editar a este tipo de personal.');
        }

        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . $user->id,
            'numero_documento'      => 'required|string|max:20|unique:users,numero_documento,' . $user->id,
            'tipo_documento'        => 'required|string|max:20',
            'role'                  => 'required|in:contratista,supervisor_contrato,ordenador_gasto,viaticos',
            'categoria_personal_id' => 'required|exists:categorias_personal,id',
            'salario_honorarios'    => 'nullable|numeric|min:0',
            'numero_cuenta_tipo'    => 'nullable|string|max:100',
            'numero_contrato'       => 'required|string|max:100',
            'anio_contrato'         => 'required|numeric|digits:4',
            'fecha_vencimiento'     => 'required|date',
            'objeto_contractual'    => 'required|string',
            'supervisor_id'         => 'nullable|exists:lideres_de_proceso,id',
            'ordenador_id'          => 'nullable|exists:lideres_de_proceso,id',
        ]);

        $user->update($request->all());

        return redirect()->route('viaticos.personal.index')
            ->with('success', 'Personal actualizado correctamente.');
    }

    /**
     * Elimina un registro de personal
     */
    public function destroy(User $user)
    {
        // Seguridad: Solo permitir eliminar si el usuario es contratista
        if ($user->role !== 'contratista') {
            return redirect()->route('viaticos.personal.index')
                ->with('error', 'No tiene permisos para eliminar a este tipo de personal.');
        }

        // Seguridad: No permitir eliminar si tiene agendas relacionadas (histórico)
        if ($user->agendas()->count() > 0) {
            return redirect()->route('viaticos.personal.index')
                ->with('error', 'No se puede eliminar el personal porque tiene agendas registradas en el sistema.');
        }

        $user->delete();

        return redirect()->route('viaticos.personal.index')
            ->with('success', 'Personal eliminado correctamente.');
    }

    /**
     * Verifica si un documento ya existe en la base de datos (AJAX)
     */
    public function checkDocument(Request $request)
    {
        $exists = \App\Models\User::where('numero_documento', $request->documento)->exists();
        return response()->json(['exists' => $exists]);
    }
}
