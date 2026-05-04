<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;
use App\Models\LiderDeProceso;

class ViaticosController extends Controller
{
    public function index(Request $request)
    {
        // Traemos las agendas que están en revisión técnica (APROBADA_SUPERVISOR)
        // Y también las enviadas o liquidadas para el historial
        $baseQuery = AgendaDesplazamiento::with(['user', 'estado'])
            ->where(function($query) {
                $query->whereHas('estado', function ($q) {
                    $q->whereIn('nombre', ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA', 'CORRECCIÓN']);
                })->orWhereNotNull('observaciones_finanzas');
            });

        // BÚSQUEDA INTELIGENTE
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    $qu->where('name', 'like', "%$search%")
                       ->orWhere('numero_documento', 'like', "%$search%");
                })
                ->orWhere('ruta', 'like', "%$search%")
                ->orWhere('destinos', 'like', "%$search%")
                ->orWhereRaw("DATE_FORMAT(fecha_inicio, '%d/%m/%Y') LIKE ?", ["%$search%"])
                ->orWhereRaw("DATE_FORMAT(fecha_fin, '%d/%m/%Y') LIKE ?", ["%$search%"]);
            });
        }

        // FILTRO POR ESTADO (Eliminado, ahora será por pestañas)
        $activeTab = $request->get('tab', 'pendientes');
        $perPage   = (int) $request->get('per_page', 5);

        // CONTADORES PERMANENTES (Mantenemos la validación de devueltas especial para viáticos)
        $devueltasIds = (clone $baseQuery)->where(function($q) {
            $q->whereNotNull('observaciones_finanzas')
              ->whereHas('estado', function($e) {
                  $e->whereNotIn('nombre', ['APROBADA_VIATICOS', 'APROBADA']);
              });
        })->orWhereHas('estado', function($q){
            $q->where('nombre', 'CORRECCIÓN');
        })->pluck('id')->toArray();

        // OBTENCION POR PESTAÑAS (Paginadas)
        $pendientes = (clone $baseQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'APROBADA_SUPERVISOR');
        })->whereNotIn('id', $devueltasIds)
          ->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page_p')->appends($request->except('page_p'));

        $aprobadas = (clone $baseQuery)->whereHas('estado', function($q) {
            $q->whereIn('nombre', ['APROBADA_VIATICOS', 'APROBADA']);
        })->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page_a')->appends($request->except('page_a'));

        $devueltas = (clone $baseQuery)->whereIn('id', $devueltasIds)
          ->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page_d')->appends($request->except('page_d'));

        // Obtener la colección de estados para el Select
        $estadosDisponibles = \App\Models\EstadoAgenda::whereIn('nombre', ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA', 'CORRECCIÓN'])->get();

        // Obtener supervisores que tienen al menos una agenda aprobada
        $supervisores = LiderDeProceso::whereHas('agendas', function($q) {
            $q->whereHas('estado', function($e) {
                $e->whereIn('nombre', ['APROBADA_VIATICOS', 'APROBADA']);
            });
        })->orderBy('nombre', 'asc')->get();

        return view('viaticos.index', compact(
            'pendientes', 
            'aprobadas',
            'devueltas',
            'supervisores',
            'activeTab'
        ));
    }

    public function gestionar(Request $request, $id)
    {
        $agenda = AgendaDesplazamiento::with(['user', 'actividades', 'estado'])->findOrFail($id);
        $tab = $request->get('tab', 'pendientes');
        return view('viaticos.gestionar', compact('agenda', 'tab'));
    }

    public function procesar(Request $request, $id)
    {
        $agenda = AgendaDesplazamiento::findOrFail($id);

        $request->validate([
            'observaciones' => 'required_if:accion,devolver|string|max:1000|nullable',
            'accion' => 'required|in:aprobar,devolver'
        ]);

        if ($request->accion == 'aprobar') {
            $estadoLiquidada = \App\Models\EstadoAgenda::where('nombre', 'APROBADA_VIATICOS')->first();
            $agenda->update([
                'estado_id' => $estadoLiquidada->id,
                'observaciones_finanzas' => $request->observaciones
            ]);
            return redirect()->route('viaticos.index')->with('success', 'Agenda aprobada correctamente.');
        }

        if ($request->accion == 'devolver') {
            $estadoCorreccion = \App\Models\EstadoAgenda::where('nombre', 'CORRECCIÓN')->first();
            // Al devolver, el estado cambia a CORRECCIÓN para permitir edición
            $agenda->update([
                'estado_id' => $estadoCorreccion->id,
                'observaciones_finanzas' => $request->observaciones
            ]);

            return redirect()->route('viaticos.index')->with('warning', 'Agenda devuelta al usuario para corrección.');
        }

        return redirect()->back()->with('error', 'Acción no reconocida.');
    }

    public function export($id)
    {
        $agenda = AgendaDesplazamiento::with(['user', 'actividades', 'estado', 'supervisor'])->findOrFail($id);

        $filename = "Viaticos_Agenda_" . $agenda->id . ".xlsx";

        // Calcular días del desplazamiento
        $inicio   = \Carbon\Carbon::parse($agenda->fecha_inicio);
        $fin      = \Carbon\Carbon::parse($agenda->fecha_fin);
        $nroDias  = $inicio->diffInDays($fin) + 1;

        $fechas = $inicio->format('d/m/Y') . ' al ' . $fin->format('d/m/Y');

        // Datos del usuario
        $user = $agenda->user;
        $salario        = $user->salario_honorarios ?? 0;
        $valorTransporte = $agenda->actividades->where('valor_intermunicipal', '>', 0)->last()->valor_intermunicipal ?? 0;
        $entreTerminal  = 0;

        // Lugar de desplazamiento
        $destinos = '';
        if (!empty($agenda->destinos) && is_array($agenda->destinos)) {
            $destinos = implode(', ', array_unique(array_filter(array_map(fn($d) => $d['nombre'] ?? null, $agenda->destinos))));
        } else {
            $destinos = $agenda->entidad_empresa ?? '';
        }

        // Ruta de la agenda
        $ruta = $agenda->ruta ?? '';

        // Número de cuenta y tipo (campo unificado)
        $numeroCuenta = $user->numero_cuenta_tipo ?? '';

        // Crear spreadsheet con PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Viáticos');

        // Headers
        $headers = [
            'COMISIONADO', 'DOCUMENTO IDENTIDAD', 'CDP', 'OBJETO COMISION',
            'RUTA A-B-C', 'NUMERO CUENTA TIPO', 'EMPRESA / SEDE', 'FECHAS',
            'LUGAR DE DESPLAZAMIENTO', 'SALARIO U HONORARIOS', 'VIATICO DIARIO',
            'NRO DIAS', 'VIATICO', 'VALOR DE TRANSPORTE', 'ENTRETERMINAL', 'TOTAL',
            'SUPERVISOR'
        ];

        // Escribir headers en fila 1
        foreach ($headers as $col => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->getCell($colLetter . '1')->setValue($header);
        }

        // Estilo del header: fondo amarillo, negrita, centrado
        $headerRange = 'A1:Q1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FBFF18'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Datos en fila 2
        $data = [
            $user->name ?? 'N/A',
            $user->numero_documento ?? 'N/A',
            $agenda->cdp ?? '',
            $agenda->objetivo_desplazamiento ?? '',
            '', // RUTA A-B-C vacía
            $numeroCuenta,
            $agenda->entidad_empresa ?? '',
            $fechas,
            $agenda->ruta ?? '',
            $salario,
            '',  // VIATICO DIARIO vacío
            $nroDias,
            '',  // VIATICO vacío
            $valorTransporte,
            56813, // ENTRETERMINAL FIJO
            '',  // TOTAL vacío
            $agenda->supervisor->nombre ?? 'N/A',
        ];

        foreach ($data as $col => $value) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->getCell($colLetter . '2')->setValue($value);
        }

        // Formato numérico para columnas de dinero (J=10, N=14)
        $sheet->getStyle('J2')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('N2')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('O2')->getNumberFormat()->setFormatCode('#,##0');

        // Bordes en fila de datos
        $sheet->getStyle('A2:Q2')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Auto-ajustar ancho de columnas
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generar y descargar el archivo .xlsx
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No se seleccionaron agendas para exportar.');
        }

        $agendas = AgendaDesplazamiento::with(['user', 'actividades', 'estado', 'supervisor'])
            ->whereIn('id', $ids)
            ->get();

        if ($agendas->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron las agendas seleccionadas.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Viáticos Masivo');

        $headers = [
            'COMISIONADO', 'DOCUMENTO IDENTIDAD', 'CDP', 'OBJETO COMISION',
            'RUTA A-B-C', 'NUMERO CUENTA TIPO', 'EMPRESA / SEDE', 'FECHAS',
            'LUGAR DE DESPLAZAMIENTO', 'SALARIO U HONORARIOS', 'VIATICO DIARIO',
            'NRO DIAS', 'VIATICO', 'VALOR DE TRANSPORTE', 'ENTRETERMINAL', 'TOTAL',
            'SUPERVISOR'
        ];

        foreach ($headers as $col => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->getCell($colLetter . '1')->setValue($header);
        }

        $headerRange = 'A1:Q1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBFF18']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ]);

        $currentRow = 2;
        foreach ($agendas as $agenda) {
            $user = $agenda->user;
            $inicio = \Carbon\Carbon::parse($agenda->fecha_inicio);
            $fin = \Carbon\Carbon::parse($agenda->fecha_fin);
            $nroDias = $inicio->diffInDays($fin) + 1;
            $fechas = $inicio->format('d/m/Y') . ' al ' . $fin->format('d/m/Y');
            $valorTransporte = $agenda->actividades->where('valor_intermunicipal', '>', 0)->last()->valor_intermunicipal ?? 0;

            $data = [
                $user->name ?? 'N/A',
                $user->numero_documento ?? 'N/A',
                $agenda->cdp ?? '',
                $agenda->objetivo_desplazamiento ?? '',
                '', // RUTA A-B-C vacía
                $user->numero_cuenta_tipo ?? '',
                $agenda->entidad_empresa ?? '',
                $fechas,
                $agenda->ruta ?? '',
                $user->salario_honorarios ?? 0,
                '',  // VIATICO DIARIO
                $nroDias,
                '',  // VIATICO
                $valorTransporte,
                56813, // ENTRETERMINAL FIJO
                '',  // TOTAL
                $agenda->supervisor->nombre ?? 'N/A',
            ];

            foreach ($data as $col => $value) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
                $sheet->getCell($colLetter . $currentRow)->setValue($value);
            }

            $sheet->getStyle('J' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('N' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('O' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A' . $currentRow . ':Q' . $currentRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            $currentRow++;
        }

        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Viaticos_Masivo_" . date('Ymd_His') . ".xlsx";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function getAgendasBySupervisor(Request $request)
    {
        $supervisorId = $request->input('supervisor_id');
        if (!$supervisorId) {
            return response()->json(['success' => false, 'message' => 'ID de supervisor no proporcionado.']);
        }

        $agendas = AgendaDesplazamiento::with(['user', 'estado'])
            ->where('supervisor_id', $supervisorId)
            ->whereHas('estado', function($q) {
                $q->whereIn('nombre', ['APROBADA_VIATICOS', 'APROBADA']);
            })
            ->get()
            ->map(function($agenda) {
                return [
                    'id' => $agenda->id,
                    'contratista' => $agenda->user->name ?? 'N/A',
                    'destino' => $agenda->ruta ?? 'N/A',
                    'fecha_inicio' => $agenda->fecha_inicio->format('d/m/Y'),
                    'estado' => $agenda->estado->nombre
                ];
            });

        return response()->json([
            'success' => true,
            'agendas' => $agendas
        ]);
    }
}