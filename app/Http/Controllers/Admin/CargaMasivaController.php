<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LiderDeProceso;
use App\Models\CategoriaPersonal;
use App\Models\CargaMasivaHistorial;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

class CargaMasivaController extends Controller
{
    /**
     * Muestra la vista de carga masiva.
     */
    public function index()
    {
        $historial = CargaMasivaHistorial::with('user')->latest()->get();
        return view('admin.carga_masiva.index', compact('historial'));
    }

    /**
     * Procesa la importación del archivo Excel o CSV.
     */
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('archivo');
        $path = $file->getRealPath();

        try {
            // Asegurar detección de finales de línea para CSVs de diferentes sistemas
            ini_set('auto_detect_line_endings', true);

            $spreadsheet = IOFactory::load($path);
            $processedData = [];
            $reportData = [['Hoja', 'Nombre', 'Email', 'Contraseña', '¿Es Nuevo?', 'Rol']];
            $totalProcesadosExito = 0;

            DB::beginTransaction();

            $allRowsToProcess = [];

            foreach ($spreadsheet->getAllSheets() as $worksheet) {
                $sheetName = $worksheet->getTitle();
                $sheetRowsRaw = [];
                foreach ($worksheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    $sheetRowsRaw[] = $cells;
                }

                if (count($sheetRowsRaw) < 2) continue;

                $headersRaw = array_shift($sheetRowsRaw);
                $headers = [];
                $headerCounts = [];
                foreach ($headersRaw as $header) {
                    $h = trim((string)$header);
                    $h = strtolower($h);
                    $h = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $h);
                    $h = preg_replace('/[^a-z0-9_]/', '_', $h);
                    $h = trim($h, '_');
                    if ($h === '') $h = 'columna_vacia_' . uniqid();
                    if (!isset($headerCounts[$h])) {
                        $headerCounts[$h] = 0;
                        $headers[] = $h;
                    } else {
                        $headerCounts[$h]++;
                        $headers[] = $h . '_' . $headerCounts[$h];
                    }
                }

                foreach ($sheetRowsRaw as $row) {
                    if (empty(filter_filter_empty($row))) continue;
                    
                    // Asegurar que la fila tenga el mismo número de columnas que los encabezados
                    if (count($headers) !== count($row)) {
                        $row = count($row) < count($headers) ? array_pad($row, count($headers), '') : array_slice($row, 0, count($headers));
                    }

                    $data = array_combine($headers, $row);
                    $allRowsToProcess[] = [
                        'sheet' => $sheetName,
                        'data' => $data
                    ];
                }
            }

            // PRE-VALIDACIÓN: Verificar que no haya campos críticos vacíos
            foreach ($allRowsToProcess as $index => $item) {
                $data = $item['data'];
                $rowNum = $index + 2; // +1 por el encabezado, +1 por índice 0
                
                $numeroDocumento = $this->cleanDocument($data['numero_documento'] ?? $data['documento'] ?? $data['cedula'] ?? $data['identificacion'] ?? '');
                $email = trim((string)($data['email'] ?? $data['correo'] ?? $data['correo_electronico'] ?? ''));
                $nombre = trim((string)($data['nombre'] ?? $data['nombre_completo'] ?? $data['usuario'] ?? ''));

                if (empty($numeroDocumento)) {
                    return response()->json(['success' => false, 'message' => "Error en la fila {$rowNum}: El 'Número de Documento' está vacío."], 400);
                }
                if (empty($email)) {
                    return response()->json(['success' => false, 'message' => "Error en la fila {$rowNum}: El 'Email' está vacío."], 400);
                }
                if (empty($nombre)) {
                    return response()->json(['success' => false, 'message' => "Error en la fila {$rowNum}: El 'Nombre' está vacío."], 400);
                }

                // Determinar rol para validaciones específicas
                $role = 'contratista';
                $tieneCategoria = isset($data['categoria_nombre']) && trim($data['categoria_nombre']) !== '';
                $roleSource = null;
                foreach (['role', 'rol', 'tipo_usuario'] as $key) {
                    if (isset($data[$key]) && trim($data[$key]) !== '') { $roleSource = $key; break; }
                }
                if (!$roleSource && !$tieneCategoria && isset($data['tipo']) && trim($data['tipo']) !== '') $roleSource = 'tipo';

                if ($roleSource) {
                    $roleValue = strtoupper(trim($data[$roleSource]));
                    $role = match(true) {
                        str_contains($roleValue, 'SUPERVISOR') => 'supervisor_contrato',
                        str_contains($roleValue, 'ORDENADOR') => 'ordenador_gasto',
                        str_contains($roleValue, 'VIATICOS')  => 'viaticos',
                        default => 'contratista'
                    };
                }

                if ($role === 'contratista') {
                    if (!$tieneCategoria) {
                        return response()->json(['success' => false, 'message' => "Error en la fila {$rowNum}: El campo 'categoria_nombre' es obligatorio para contratistas."], 400);
                    }
                    if (empty($data['doc_supervisor'] ?? '')) {
                        return response()->json(['success' => false, 'message' => "Error en la fila {$rowNum}: El 'doc_supervisor' es obligatorio para contratistas."], 400);
                    }
                    if (empty($data['doc_ordenador'] ?? '')) {
                        return response()->json(['success' => false, 'message' => "Error en la fila {$rowNum}: El 'doc_ordenador' es obligatorio para contratistas."], 400);
                    }
                } else {
                    // Es un líder de proceso
                    if (empty($data['cargo'] ?? '')) {
                        return response()->json(['success' => false, 'message' => "Error en la fila {$rowNum}: El 'cargo' es obligatorio para líderes de proceso."], 400);
                    }
                    if (empty($data['tipo'] ?? '')) {
                        return response()->json(['success' => false, 'message' => "Error en la fila {$rowNum}: El 'tipo' (SUPERVISOR/ORDENADOR/VIATICOS) es obligatorio para líderes."], 400);
                    }
                }
            }

            // PASO 1: Crear/Actualizar todos los usuarios y líderes de proceso básicos
            foreach ($allRowsToProcess as $item) {
                try {
                    $data = $item['data'];
                    $numeroDocumento = $this->cleanDocument($data['numero_documento'] ?? $data['documento'] ?? $data['cedula'] ?? $data['identificacion'] ?? '');
                    $email = trim(strtolower((string)($data['email'] ?? $data['correo'] ?? $data['correo_electronico'] ?? '')));
                    $nombre = trim(strtoupper((string)($data['nombre'] ?? $data['nombre_completo'] ?? $data['usuario'] ?? '')));

                    // Ya validados arriba, pero mantenemos limpieza básica por seguridad
                    if (!$numeroDocumento || !$email) continue;

                    // Determinar rol
                    $role = 'contratista';
                    $tieneCategoria = isset($data['categoria_nombre']) && trim($data['categoria_nombre']) !== '';
                    $roleSource = null;
                    foreach (['role', 'rol', 'tipo_usuario'] as $key) {
                        if (isset($data[$key]) && trim($data[$key]) !== '') { $roleSource = $key; break; }
                    }
                    if (!$roleSource && !$tieneCategoria && isset($data['tipo']) && trim($data['tipo']) !== '') $roleSource = 'tipo';

                    if ($roleSource) {
                        $roleValue = strtoupper(trim($data[$roleSource]));
                        $role = match(true) {
                            str_contains($roleValue, 'SUPERVISOR') => 'supervisor_contrato',
                            str_contains($roleValue, 'ORDENADOR') => 'ordenador_gasto',
                            str_contains($roleValue, 'VIATICOS')  => 'viaticos',
                            default => 'contratista'
                        };
                    }

                    $user = User::where('numero_documento', $numeroDocumento)->first();
                    $esNuevo = "No";
                    $plainPassword = "******** (Existente)";

                    if (!$user) {
                        // VALIDACIÓN: No permitir carga si faltan datos esenciales para UN NUEVO REGISTRO
                        if (!$nombre || !$email) {
                            $reportData[] = [$item['sheet'], $nombre ?: 'N/A', $email ?: 'N/A', 'N/A', 'Error: Datos incompletos (Nombre/Email)', 'N/A'];
                            continue;
                        }

                        $esNuevo = "Sí";
                        $randomDigits = random_int(10, 99);
                        $plainPassword = $numeroDocumento . $randomDigits;
                        $user = User::create([
                            'name' => $nombre, 'email' => $email, 'password' => Hash::make($plainPassword),
                            'tipo_documento' => $data['tipo_documento'] ?? 'CC', 'numero_documento' => $numeroDocumento,
                            'role' => $role, 'numero_cuenta_tipo' => $data['numero_cuenta_tipo'] ?? null,
                        ]);
                    } else {
                        // SI EXISTE, NO SOBREESCRIBIMOS (Requerimiento del usuario)
                        $esNuevo = "No (Ya existe)";
                        $plainPassword = "******** (Sin cambios)";
                    }

                    if (in_array($role, ['supervisor_contrato', 'ordenador_gasto', 'viaticos'])) {
                        $lider = LiderDeProceso::where('numero_documento', $numeroDocumento)->first();
                        if (!$lider) {
                            LiderDeProceso::create([
                                'numero_documento' => $numeroDocumento,
                                'nombre' => $nombre, 'email' => $email, 'tipo_documento' => $data['tipo_documento'] ?? 'CC',
                                'cargo' => $data['cargo'] ?? 'Líder de Proceso', 'tipo' => strtoupper($data['tipo'] ?? 'OTRO'),
                                'numero_cuenta_tipo' => $data['numero_cuenta_tipo'] ?? null,
                            ]);
                        }
                    }

                    // Guardar para el reporte final (esto se actualizará en el paso 2 si es contratista)
                    $processedData[$numeroDocumento] = [
                        'nombre' => $nombre, 'correo' => $email, 'password' => $plainPassword, 'es_nuevo' => $esNuevo, 'rol' => $role, 'sheet' => $item['sheet']
                    ];
                } catch (\Exception $e) {
                    $reportData[] = [$item['sheet'], $item['data']['nombre'] ?? 'Desconocido', 'N/A', 'N/A', 'Error P1: ' . $e->getMessage(), 'N/A'];
                }
            }

            $finalJsonData = [];

            // PASO 2: Procesar relaciones de contratistas y generar reporte final
            foreach ($allRowsToProcess as $item) {
                try {
                    $data = $item['data'];
                    $numeroDocumento = $this->cleanDocument($data['numero_documento'] ?? $data['documento'] ?? $data['cedula'] ?? $data['identificacion'] ?? '');
                    if (!$numeroDocumento) continue;

                    $user = User::where('numero_documento', $numeroDocumento)->first();
                    if (!$user) continue;

                    $tieneCategoria = isset($data['categoria_nombre']) && trim($data['categoria_nombre']) !== '';
                    if ($tieneCategoria) {
                        $this->procesarContratista($user, $data);
                    }

                    if (isset($processedData[$numeroDocumento])) {
                        $p = $processedData[$numeroDocumento];
                        $reportData[] = [$p['sheet'], $p['nombre'], $p['correo'], '="' . $p['password'] . '"', $p['es_nuevo'], $p['rol']];
                        
                        $finalJsonData[] = [
                            'nombre' => $p['nombre'],
                            'correo' => $p['correo'],
                            'password' => $p['password'],
                            'es_nuevo' => $p['es_nuevo']
                        ];

                        $totalProcesadosExito++;
                        unset($processedData[$numeroDocumento]); 
                    }
                } catch (\Exception $e) {
                    $reportData[] = [$item['sheet'], $item['data']['nombre'] ?? 'Desconocido', 'N/A', 'N/A', 'Error P2: ' . $e->getMessage(), 'N/A'];
                }
            }

            if ($totalProcesadosExito === 0 && empty($reportData)) {
                return response()->json(['success' => false, 'message' => 'No se encontraron datos procesables en ninguna hoja del archivo.']);
            }

            DB::commit();

            // Guardar reporte con nombre único para el historial
            $originalFilename = $request->file('archivo')->getClientOriginalName();
            $reportFilename = 'reporte_carga_' . date('Ymd_His') . '_' . uniqid() . '.csv';
            $this->guardarReporte($reportData, $reportFilename);
            
            // También guardar el reporte "general" para descarga inmediata
            $this->guardarReporte($reportData, 'reporte_carga_masiva.csv');

            // Crear registro en el historial
            CargaMasivaHistorial::create([
                'nombre_archivo' => $originalFilename,
                'ruta_reporte' => $reportFilename,
                'total_registros' => count($allRowsToProcess),
                'total_exito' => $totalProcesadosExito,
                'total_errores' => (count($reportData) - 1) - $totalProcesadosExito,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Procesamiento masivo completado con éxito.',
                'total_exito' => $totalProcesadosExito,
                'total_reporte' => count($reportData) - 1,
                'data' => $finalJsonData
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    private function procesarContratista($user, $data)
    {
        $categoria = CategoriaPersonal::where('nombre', trim($data['categoria_nombre']))->first();
        $supervisor = LiderDeProceso::where('numero_documento', $this->cleanDocument($data['doc_supervisor'] ?? ''))->first();
        $ordenador = LiderDeProceso::where('numero_documento', $this->cleanDocument($data['doc_ordenador'] ?? ''))->first();

        $anio = trim((string)($data['anio_contrato'] ?? date('Y')));
        $anio = str_replace('.0', '', $anio);
        if (strlen($anio) > 4) $anio = substr($anio, -4);
        
        $rawSalario = trim((string)($data['salario_honorarios'] ?? 0));
        // Quitar símbolos de moneda, puntos y comas de formato
        $salario = preg_replace('/[^0-9]/', '', $rawSalario);
        
        // Si después de limpiar no queda nada o no es numérico, poner 0
        if (!is_numeric($salario) || $salario === '') {
            $salario = 0;
        }

        $user->update([
            'salario_honorarios' => $salario,
            'numero_contrato' => $data['numero_contrato'] ?? null,
            'anio_contrato' => $anio,
            'fecha_vencimiento' => $this->formatDate($data['fecha_vencimiento'] ?? null),
            'objeto_contractual' => $data['objeto_contractual'] ?? null,
            'categoria_personal_id' => $categoria?->id,
            'supervisor_id' => $supervisor?->id,
            'ordenador_id' => $ordenador?->id,
        ]);
    }

    private function cleanDocument($doc)
    {
        $doc = trim((string)$doc);
        if (!$doc) return '';

        // Manejar notación científica (ej: 1.1105E+11 o 1,1105E+11)
        if (stripos($doc, 'E+') !== false) {
            $doc = str_replace(',', '.', $doc); // Asegurar punto decimal para float
            $doc = sprintf('%.0f', (float)$doc); // Convertir a string sin notación científica
        }

        // Quitar decimales generados por Excel (.0) y otros separadores de formato
        $doc = str_replace(['.0', '.', ','], '', $doc);

        return $doc;
    }


    private function formatDate($date)
    {
        if (!$date) return null;

        try {
            // 1. Manejar formato numérico de Excel
            if (is_numeric($date)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            }

            // 2. Limpiar y separar por / o - o .
            $parts = preg_split('/[\/\-\.]/', $date);
            
            if (count($parts) === 3) {
                $p1 = (int)$parts[0];
                $p2 = (int)$parts[1];
                $p3 = (int)$parts[2];

                // Caso A: El año está al final (ej: 14/12/2026)
                if ($p3 > 1000) {
                    // Si el segundo número es > 12, es imposible que sea el mes (ej: 12/14/2026)
                    // Por lo tanto es MM/DD/YYYY
                    if ($p2 > 12) {
                        return sprintf('%04d-%02d-%02d', $p3, $p1, $p2);
                    }
                    // Si el primer número es > 12, es imposible que sea el mes (ej: 14/12/2026)
                    // Por lo tanto es DD/MM/YYYY
                    if ($p1 > 12) {
                        return sprintf('%04d-%02d-%02d', $p3, $p2, $p1);
                    }
                    // Por defecto para ambigüedad, asumimos DD/MM/YYYY (Estándar local)
                    return sprintf('%04d-%02d-%02d', $p3, $p2, $p1);
                }

                // Caso B: El año está al principio (ej: 2026/12/14)
                if ($p1 > 1000) {
                    // Si el segundo número es > 12, es YYYY/DD/MM (ej: 2026/14/12)
                    if ($p2 > 12) {
                        return sprintf('%04d-%02d-%02d', $p1, $p3, $p2);
                    }
                    // Si no, asumimos YYYY/MM/DD
                    return sprintf('%04d-%02d-%02d', $p1, $p2, $p3);
                }
            }
        } catch (\Exception $e) {
            // Fallback silencioso
        }

        return $date;
    }

    private function guardarReporte($reportData, $filename = 'reporte_carga_masiva.csv')
    {
        $path = storage_path('app/' . $filename);
        $file = fopen($path, 'w');
        // Añadir BOM para que Excel reconozca UTF-8 correctamente
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        foreach ($reportData as $line) {
            fputcsv($file, $line, ';');
        }
        fclose($file);
    }

    /**
     * Descarga el reporte de credenciales generado.
     */
    public function descargarReporte()
    {
        $path = storage_path('app/reporte_carga_masiva.csv');
        if (!file_exists($path)) {
            return back()->with('error', 'No hay un reporte reciente para descargar.');
        }
        return response()->download($path, 'Reporte_Credenciales_Reciente.csv');
    }

    /**
     * Descarga un reporte específico del historial.
     */
    public function descargarReporteHistorial($id)
    {
        $historial = CargaMasivaHistorial::findOrFail($id);
        $path = storage_path('app/' . $historial->ruta_reporte);
        
        if (!file_exists($path)) {
            return back()->with('error', 'El archivo del reporte ya no existe en el servidor.');
        }
        
        return response()->download($path, 'Reporte_' . str_replace(' ', '_', $historial->nombre_archivo) . '.csv');
    }
}

/**
 * Helper para filtrar filas vacías de PhpSpreadsheet
 */
function filter_filter_empty($row) {
    if (!is_array($row)) return [];
    return array_filter($row, function($value) {
        return $value !== null && trim((string)$value) !== '';
    });
}
