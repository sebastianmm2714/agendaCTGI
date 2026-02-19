<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;
use App\Models\Departamento;
use App\Models\Municipio;

class FormularioController extends Controller
{
    public function index($id = null)
    {
        $agenda = null;
        if ($id) {
            $agenda = AgendaDesplazamiento::with('actividades')->findOrFail($id);
        }

        $departamentos = Departamento::orderBy('nombre')->get();

        return view('formulario', compact('agenda'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string|max:50',
            'fecha_elaboracion' => 'required|date',

            'numero_contrato' => 'required|numeric',
            'anio_contrato' => 'required|integer',
            'fecha_vencimiento' => 'required|date|after_or_equal:today',
            'clasificacion_informacion' => 'required|in:publica,clasificada,reservada',

            'cargo' => 'required|in:Contratista,Servidor_Publico',
            'objetivo_contractual' => 'required|string',

            'destino_departamento_id' => 'required|exists:departamentos,id',
            'destino_municipio_id' => 'nullable|exists:municipios,id',


            'entidad_empresa' => 'required|string|max:120',
            'contacto' => 'required|string|max:120',

            'fecha_inicio_desplazamiento' => 'required|date|after_or_equal:today',
            'fecha_fin_desplazamiento' => 'required|date|after_or_equal:fecha_inicio_desplazamiento',

            'objetivo_desplazamiento' => 'required|string|max:160',

            'obligaciones_contrato' => 'required|array|min:1',
            'obligaciones_contrato.*' => 'required|string|max:500',

            'firma_contratista' => 'nullable|image|max:4096',
        ]);




        /* ================= DATOS CALCULADOS ================= */

        $departamento = Departamento::find($request->destino_departamento_id);
        $municipio = $request->destino_municipio_id
            ? Municipio::find($request->destino_municipio_id)
            : null;

        $data['ciudad_destino'] = $municipio
            ? $municipio->nombre
            : $departamento->nombre;

        $data['ruta'] = 'MEDELLIN - ' . $data['ciudad_destino'] . ' - MEDELLIN';

        $data['direccion_general'] = 'ANTIOQUIA';
        $data['dependencia_centro'] = 'CENTRO TEXTIL Y DE GESTION INDUSTRIAL';

        $data['obligaciones_contrato'] = $request->obligaciones_contrato;

        $data['estado'] = 'BORRADOR';
        $data['user_id'] = auth()->id();

        if ($request->hasFile('firma_contratista')) {
            $data['firma_contratista'] = $request
                ->file('firma_contratista')
                ->store('firmas', 'public');
        } elseif ($request->filled('firma_base64')) {
            // Procesar firma en base64
            $imageData = $request->firma_base64;
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $fileName = 'firmas/firma_' . time() . '_' . uniqid() . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, base64_decode($imageData));
            $data['firma_contratista'] = $fileName;
        }

        $agenda = AgendaDesplazamiento::create($data);

        return redirect()->route('formulario', $agenda->id)
            ->with('success', 'Agenda creada. Ahora puede agregar actividades.');
    }

    public function pdf($id)
    {
        $agenda = AgendaDesplazamiento::with('actividades')->findOrFail($id);
        return view('agenda.pdf', compact('agenda'));
    }

    public function enviar($id)
    {
        $agenda = AgendaDesplazamiento::where('user_id', auth()->id())->findOrFail($id);

        if ($agenda->estado !== 'BORRADOR') {
            return back()->with('error', 'Esta agenda ya ha sido enviada o procesada.');
        }

        $agenda->update(['estado' => 'ENVIADA']);

        return redirect()->route('reportar-dia')->with('success', 'Agenda enviada a coordinación para revisión.');
    }
}
