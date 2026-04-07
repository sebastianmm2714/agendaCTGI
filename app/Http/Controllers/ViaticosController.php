<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaDesplazamiento;

class ViaticosController extends Controller
{
    public function index()
    {
        // Traemos las agendas que están en revisión técnica (APROBADA_SUPERVISOR)
        // Y también las enviadas o liquidadas para el historial
        $agendas = AgendaDesplazamiento::with(['user', 'estado'])
            ->whereHas('estado', function ($q) {
                $q->whereIn('nombre', ['APROBADA_SUPERVISOR', 'APROBADA_VIATICOS', 'APROBADA_ORDENADOR', 'APROBADA', 'CORRECCIÓN']);
            })
            ->orWhere(function ($q) {
                $q->whereNotNull('observaciones_finanzas');
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('viaticos.index', compact('agendas'));
    }

    public function gestionar($id)
    {
        $agenda = AgendaDesplazamiento::with(['user', 'actividades', 'estado'])->findOrFail($id);
        return view('viaticos.gestionar', compact('agenda'));
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
            return redirect()->back()->with('success', 'Agenda aprobada correctamente.');
        }

        if ($request->accion == 'devolver') {
            $estadoCorreccion = \App\Models\EstadoAgenda::where('nombre', 'CORRECCIÓN')->first();
            // Al devolver, el estado cambia a CORRECCIÓN para permitir edición
            $agenda->update([
                'estado_id' => $estadoCorreccion->id,
                'observaciones_finanzas' => $request->observaciones
            ]);
            return redirect()->back()->with('warning', 'Agenda devuelta al usuario para corrección.');
        }

        return redirect()->back()->with('error', 'Acción no reconocida.');
    }

    public function export($id)
    {
        $agenda = AgendaDesplazamiento::with(['user', 'actividades', 'estado'])->findOrFail($id);

        $filename = "Viaticos_Agenda_" . $agenda->id . ".xlsx";

        // Calcular días del desplazamiento
        $inicio   = \Carbon\Carbon::parse($agenda->fecha_inicio);
        $fin      = \Carbon\Carbon::parse($agenda->fecha_fin);
        $nroDias  = $inicio->diffInDays($fin) + 1;

        $fechas = $inicio->format('d/m/Y') . ' al ' . $fin->format('d/m/Y');

        // Datos del usuario
        $user = $agenda->user;
        $salario        = $user->salario_honorarios ?? 0;
        $valorTransporte = $agenda->valor_intermunicipal ?? 0;
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
            'NRO DIAS', 'VIATICO', 'VALOR DE TRANSPORTE', 'ENTRETERMINAL', 'TOTAL'
        ];

        // Escribir headers en fila 1
        foreach ($headers as $col => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->getCell($colLetter . '1')->setValue($header);
        }

        // Estilo del header: fondo amarillo, negrita, centrado
        $headerRange = 'A1:P1';
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
            $user->objeto_contractual ?? '',
            $ruta,
            $numeroCuenta,
            $agenda->entidad_empresa ?? '',
            $fechas,
            $destinos,
            $salario,
            '',  // VIATICO DIARIO vacío
            $nroDias,
            '',  // VIATICO vacío
            $valorTransporte,
            $entreTerminal,
            '',  // TOTAL vacío
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
        $sheet->getStyle('A2:P2')->applyFromArray([
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
        foreach (range('A', 'P') as $col) {
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
}