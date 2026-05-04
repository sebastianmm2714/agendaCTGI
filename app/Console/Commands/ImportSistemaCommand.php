<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\LiderDeProceso;
use App\Models\CategoriaPersonal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ImportSistemaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-sistema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa líderes de proceso y contratistas desde archivos CSV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando proceso de importación masiva...');

        $lideresPath = base_path('database/data/LideresDeProceso.csv');
        $contratistasPath = base_path('database/data/Instructores.csv');

        // 1. IMPORTAR LÍDERES DE PROCESO
        if (!file_exists($lideresPath)) {
            $this->error("No se encontró el archivo de líderes de proceso en: $lideresPath");
            return;
        }

        $this->line('Cargando Líderes de Proceso...');
        $this->importLideresDeProceso($lideresPath);

        // 2. IMPORTAR CONTRATISTAS
        if (!file_exists($contratistasPath)) {
            $this->error("No se encontró el archivo de contratistas en: $contratistasPath");
            return;
        }

        $this->line('Cargando Contratistas...');
        $this->importContratistas($contratistasPath);

        $this->info('¡Importación completada con éxito!');
        $this->info('Reporte de Contratistas: storage/app/reporte_carga_usuarios.csv');
        $this->info('Reporte de Líderes de Proceso: storage/app/reporte_carga_lideres.csv');
    }

    private function importLideresDeProceso($path)
    {
        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle); // Leer encabezados
        $reportData = [['Nombre', 'Email', 'Contraseña', '¿Es Nuevo?', '¿Se cambió clave?']];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($headers) !== count($row)) {
                $this->error("Error en fila de Líderes de Proceso: El número de columnas no coincide. Saltando...");
                continue;
            }
            $data = array_combine($headers, $row);

            $role = match($data['tipo']) {
                'SUPERVISOR' => 'supervisor_contrato',
                'ORDENADOR' => 'ordenador_gasto',
                'VIATICOS' => 'viaticos',
                default => 'contratista'
            };

            // Verificar si el usuario ya existe para no cambiarle la clave
            $existingUser = User::where('numero_documento', $data['numero_documento'])->first();
            $esNuevo = "No";
            $seCambioClave = "No";
            $plainPassword = "******** (Manteniendo anterior)";

            if (!$existingUser) {
                // Generar Contraseña solo para nuevos
                $esNuevo = "Sí";
                $seCambioClave = "Sí";
                $randomDigits = random_int(10, 99);
                $plainPassword = $data['numero_documento'] . $randomDigits;
                
                User::create([
                    'name' => $data['nombre'],
                    'email' => $data['email'],
                    'password' => Hash::make($plainPassword),
                    'tipo_documento' => $data['tipo_documento'] ?? 'CC',
                    'numero_cuenta_tipo' => $data['numero_cuenta_tipo'] ?? null,
                    'role' => $role,
                    'numero_documento' => $data['numero_documento']
                ]);
            } else {
                // Actualizar solo datos, sin tocar password
                $existingUser->update([
                    'name' => $data['nombre'],
                    'email' => $data['email'],
                    'tipo_documento' => $data['tipo_documento'] ?? 'CC',
                    'numero_cuenta_tipo' => $data['numero_cuenta_tipo'] ?? null,
                    'role' => $role
                ]);
            }

            // Crear o actualizar líder de proceso
            LiderDeProceso::updateOrCreate(
                ['numero_documento' => $data['numero_documento']],
                [
                    'nombre' => $data['nombre'],
                    'email' => $data['email'],
                    'tipo_documento' => $data['tipo_documento'] ?? 'CC',
                    'cargo' => $data['cargo'],
                    'tipo' => $data['tipo'],
                    'numero_cuenta_tipo' => $data['numero_cuenta_tipo'] ?? null,
                ]
            );

            $reportData[] = [$data['nombre'], $data['email'], $plainPassword, $esNuevo, $seCambioClave];
        }
        fclose($handle);

        // Guardar reporte de líderes de proceso
        $reportPath = storage_path('app/reporte_carga_lideres.csv');
        $file = fopen($reportPath, 'w');
        foreach ($reportData as $line) {
            fputcsv($file, $line);
        }
        fclose($file);
    }

    private function importContratistas($path)
    {
        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle);
        
        $reportData = [['Nombre', 'Email', 'Contraseña', '¿Es Nuevo?', '¿Se cambió clave?']];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($headers) !== count($row)) {
                $this->error("Error en fila de Contratistas: El número de columnas no coincide. Saltando...");
                continue;
            }
            $data = array_combine($headers, $row);

            // 1. Buscar Categoría
            $categoria = CategoriaPersonal::where('nombre', $data['categoria_nombre'])->first();
            if (!$categoria) {
                $this->error("Categoría '{$data['categoria_nombre']}' no encontrada. Saltando...");
                continue;
            }

            // 2. Buscar Supervisor y Ordenador por Documento
            $supervisor = LiderDeProceso::where('numero_documento', $data['doc_supervisor'])->first();
            $ordenador = LiderDeProceso::where('numero_documento', $data['doc_ordenador'])->first();

            // Verificar si el usuario ya existe
            $existingUser = User::where('numero_documento', $data['numero_documento'])->first();
            $esNuevo = "No";
            $seCambioClave = "No";
            $plainPassword = "******** (Manteniendo anterior)";

            // 4. Limpieza de fechas y años
            $anio = $data['anio_contrato'];
            if (strlen($anio) > 4) {
                $anio = substr($anio, -4);
            }

            $vencimiento = $data['fecha_vencimiento'];
            if ($vencimiento && str_contains($vencimiento, '/')) {
                $parts = explode('/', $vencimiento);
                if (count($parts) === 3) {
                    $vencimiento = "{$parts[2]}-{$parts[1]}-{$parts[0]}";
                }
            }

            $userData = [
                'name' => $data['nombre'],
                'email' => $data['email'],
                'tipo_documento' => $data['tipo_documento'] ?? 'CC',
                'salario_honorarios' => str_replace(['.', ','], '', $data['salario_honorarios'] ?? 0),
                'numero_cuenta_tipo' => $data['numero_cuenta_tipo'] ?? null,
                'numero_contrato' => $data['numero_contrato'] ?? null,
                'anio_contrato' => $anio,
                'fecha_vencimiento' => $vencimiento,
                'objeto_contractual' => $data['objeto_contractual'] ?? null,
                'categoria_personal_id' => $categoria->id,
                'supervisor_id' => $supervisor?->id,
                'ordenador_id' => $ordenador?->id,
                'role' => $data['role'] ?? 'contratista',
            ];

            if (!$existingUser) {
                $esNuevo = "Sí";
                $seCambioClave = "Sí";
                $randomDigits = random_int(10, 99);
                $plainPassword = $data['numero_documento'] . $randomDigits;
                $userData['password'] = Hash::make($plainPassword);
                $userData['numero_documento'] = $data['numero_documento'];
                User::create($userData);
            } else {
                $existingUser->update($userData);
            }

            $reportData[] = [$data['nombre'], $data['email'], $plainPassword, $esNuevo, $seCambioClave];
        }
        fclose($handle);

        // Guardar reporte
        $reportPath = storage_path('app/reporte_carga_usuarios.csv');
        $file = fopen($reportPath, 'w');
        foreach ($reportData as $line) {
            fputcsv($file, $line);
        }
        fclose($file);
    }
}
