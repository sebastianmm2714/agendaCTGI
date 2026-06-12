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
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:50|unique:users,numero_documento',
            'cargo' => 'required|string|max:100',
            'tipo' => 'required|in:SUPERVISOR,ORDENADOR,VIATICOS,LEGALIZACION',
            'numero_cuenta_tipo' => 'nullable|string|max:100',
            'password' => 'nullable|string',
        ]);

        // Mapear tipo de funcionario a rol de usuario
        $role = match($request->tipo) {
            'SUPERVISOR' => 'supervisor_contrato',
            'ORDENADOR' => 'ordenador_gasto',
            'VIATICOS' => 'viaticos',
            'LEGALIZACION' => 'legalizacion',
            default => 'contratista'
        };

        $numeroDocumento = $this->cleanDocument($request->numero_documento);
        $password = $request->input('password') ?: ($numeroDocumento . random_int(10, 99));

        // Crear el usuario primero
        $user = \App\Models\User::create([
            'name' => $request->nombre,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $numeroDocumento,
            'numero_cuenta_tipo' => $request->numero_cuenta_tipo,
            'role' => $role,
        ]);

        // Crear el líder de proceso
        LiderDeProceso::create([
            'nombre' => $request->nombre,
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
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:50',
            'cargo' => 'required|string|max:100',
            'tipo' => 'required|in:SUPERVISOR,ORDENADOR,VIATICOS,LEGALIZACION',
            'numero_cuenta_tipo' => 'nullable|string|max:100',
        ]);

        // Limpiar documentos
        $data = $request->all();
        $data['numero_documento'] = $this->cleanDocument($request->numero_documento);

        // Mapear tipo de líder a rol de usuario
        $role = match($request->tipo) {
            'SUPERVISOR'   => 'supervisor_contrato',
            'ORDENADOR'    => 'ordenador_gasto',
            'VIATICOS'     => 'viaticos',
            'LEGALIZACION' => 'legalizacion',
            default        => 'contratista'
        };

        // Actualizar líder de proceso
        $lideres_de_proceso->update($data);

        // Sincronizar el rol en users
        \App\Models\User::where('numero_documento', $data['numero_documento'])
            ->update([
                'role' => $role,
                'name' => $request->nombre,
                'tipo_documento' => $request->tipo_documento,
                'numero_cuenta_tipo' => $request->numero_cuenta_tipo,
            ]);

        return back()->with('success', 'Datos actualizados correctamente en ambas tablas.');
    }

    public function previewPdf()
    {
        // Cargar el logo SENA en base64
        $logoPath = public_path('images/sena/logoSena.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Helpers para crear objetos anónimos con propiedades
        $makeObj = fn(array $props) => (object) $props;

        // Objetos relacionados ficticios
        $contratista = $makeObj([
            'name'             => '[ NOMBRE DEL CONTRATISTA ]',
            'numero_contrato'  => '[ N° CONTRATO ]',
            'numero_documento' => '[ DOCUMENTO ]',
            'tipo_documento'   => 'CC',
            'firma'            => null,
        ]);

        $supervisor = $makeObj([
            'nombre' => '[ NOMBRE SUPERVISOR ]',
            'cargo'  => '[ CARGO SUPERVISOR ]',
            'firma'  => null,
        ]);

        $ordenador = $makeObj([
            'nombre' => '[ NOMBRE ORDENADOR ]',
            'cargo'  => '[ SUBDIRECTOR DE CENTRO ]',
            'firma'  => null,
        ]);

        // Agenda ficticia con todos los campos requeridos por pdflegalizacion.blade.php
        $agenda = $makeObj([
            'id'                         => '---',
            'numero_agenda'              => '---',
            'ruta'                       => '[ RUTA / DESTINO ]',
            'fecha_inicio'               => now()->format('Y-m-d'),
            'fecha_fin'                  => now()->format('Y-m-d'),
            'fecha_elaboracion'          => now()->format('Y-m-d'),
            'legalizado_at'              => null,
            'orden_viaje'                => '[ N° ORDEN DE VIAJE ]',
            'destinos'                   => [],
            'ciudad_destino'             => '[ CIUDAD DESTINO ]',
            'centro'                     => 'CENTRO TEXTIL Y DE GESTIÓN INDUSTRIAL',
            'objetivo_desplazamiento'    => '[ OBJETIVO DEL DESPLAZAMIENTO ]',
            'legalizacion_estado'        => null,
            'legalizacion_resultados'    => null,
            'legalizacion_compromisos'   => null,
            'legalizacion_conclusiones'  => null,
            'legalizacion_fotos'         => [],
            'legalizacion_planillas'     => [],
            'legalizacion_declaracion'   => null,
            'realiza_declaracion'        => false,
            'total_viaticos'             => 0,
            // Relaciones
            'actividades'                => \Illuminate\Support\Collection::make([]),
            'user'                       => $contratista,
            'supervisor'                 => $supervisor,
            'ordenador'                  => $ordenador,
            'legalizacion'               => null,
        ]);

        return view('legalizacion.pdflegalizacion', [
            'agenda'                   => $agenda,
            'logoBase64'               => $logoBase64,
            'contratista_firma_base64' => null,
            'supervisor_firma_base64'  => null,
            'ordenador_firma_base64'   => null,
            'isPreview'                => true,
        ]);
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
