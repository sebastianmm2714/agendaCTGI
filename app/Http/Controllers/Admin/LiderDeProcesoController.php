<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiderDeProceso;
use Illuminate\Http\Request;

class LiderDeProcesoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 10);

        $query = LiderDeProceso::orderBy('nombre');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                  ->orWhere('cargo', 'like', "%$search%")
                  ->orWhere('tipo', 'like', "%$search%")
                  ->orWhere('numero_documento', 'like', "%$search%");
            });
        }

        $lideres_de_proceso = $query->paginate($perPage)->appends($request->all());

        return view('admin.lideres_de_proceso.index', compact('lideres_de_proceso'));
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

        $numeroDocumento = $this->cleanDocument($request->numero_documento);
        $email = trim(strtolower($request->email));

        // Crear el usuario primero
        $user = \App\Models\User::create([
            'name' => $request->nombre,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($numeroDocumento),
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $numeroDocumento,
            'numero_cuenta_tipo' => $request->numero_cuenta_tipo,
            'role' => $role,
        ]);

        // Crear el líder de proceso
        LiderDeProceso::create([
            'nombre' => $request->nombre,
            'email' => $email,
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $numeroDocumento,
            'cargo' => $request->cargo,
            'tipo' => $request->tipo,
            'numero_cuenta_tipo' => $request->numero_cuenta_tipo,
        ]);

        return back()->with('success', 'LiderDeProceso y cuenta de usuario creados correctamente.');
    }

    public function update(Request $request, LiderDeProceso $lideres_de_proceso)
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

        // Limpiar documentos y normalizar email
        $data = $request->all();
        $data['numero_documento'] = $this->cleanDocument($request->numero_documento);
        $data['email'] = trim(strtolower($request->email));

        // Actualizar líder de proceso. El LiderDeProcesoObserver se encargará de sincronizar con 'users'.
        $lideres_de_proceso->update($data);

        return back()->with('success', 'Datos actualizados correctamente en ambas tablas.');
    }

    public function destroy(LiderDeProceso $lideres_de_proceso)
    {
        if ($lideres_de_proceso->usuariosSupervisor()->count() > 0 || $lideres_de_proceso->usuariosOrdenador()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un líder que tiene usuarios asignados.');
        }

        $lideres_de_proceso->delete();

        return back()->with('success', 'Líder de Proceso eliminado correctamente.');
    }

    private function cleanDocument($doc)
    {
        $doc = trim((string)$doc);
        if (!$doc) return '';

        if (stripos($doc, 'E+') !== false) {
            $doc = str_replace(',', '.', $doc);
            $doc = sprintf('%.0f', (float)$doc);
        }

        return str_replace(['.0', '.', ','], '', $doc);
    }
}
