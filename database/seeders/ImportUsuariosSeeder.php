<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CategoriaPersonal;
use App\Models\Funcionario;
use Illuminate\Support\Facades\Hash;

class ImportUsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $this->importFuncionarios();
        $this->importInstructores();
    }

    private function importFuncionarios()
    {
        $path = database_path('data/Funcionarios.csv');
        if (!file_exists($path)) return;

        $csv = array_map('str_getcsv', file($path));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            
            // 1. Crear/Actualizar Funcionario
            $funcionario = Funcionario::updateOrCreate(
                ['numero_documento' => $data['numero_documento']],
                [
                    'nombre' => $data['nombre'],
                    'email' => $data['email'],
                    'tipo_documento' => $data['tipo_documento'],
                    'cargo' => $data['cargo'],
                    'tipo' => $data['tipo'],
                ]
            );

            // 2. Crear/Actualizar Usuario para este funcionario
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nombre'],
                    'password' => Hash::make($data['numero_documento']),
                    'tipo_documento' => $data['tipo_documento'],
                    'numero_documento' => $data['numero_documento'],
                    'role' => match(strtoupper($data['rol'])) {
                        'ADMIN' => 'administrador',
                        'SUPERVISOR' => 'supervisor_contrato',
                        'ORDENADOR' => 'ordenador_gasto',
                        'VIATICOS' => 'viaticos',
                        default => 'contratista',
                    },
                ]
            );
        }
    }

    private function importInstructores()
    {
        $path = database_path('data/Instructores.csv');
        if (!file_exists($path)) return;

        $csv = array_map('str_getcsv', file($path));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            
            $categoria = CategoriaPersonal::where('nombre', $data['categoria_personal'])->first();
            $supervisor = Funcionario::where('nombre', 'like', '%' . ($data['supervisor_nombre'] ?? '---') . '%')->first();
            $ordenador = Funcionario::where('nombre', 'like', '%' . ($data['ordenador_nombre'] ?? '---') . '%')->first();

            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nombre'],
                    'password' => Hash::make($data['numero_documento']),
                    'tipo_documento' => $data['tipo_documento'],
                    'numero_documento' => $data['numero_documento'],
                    'numero_contrato' => $data['numero_contrato'],
                    'anio_contrato' => $data['anio_contrato'],
                    'fecha_vencimiento' => $data['fecha_vencimiento'],
                    'objeto_contractual' => $data['objeto_contractual'],
                    'salario_honorarios' => $data['salario_honorarios'],
                    'categoria_personal_id' => $categoria?->id,
                    'supervisor_id' => $supervisor?->id,
                    'ordenador_id' => $ordenador?->id,
                    'role' => 'contratista',
                ]
            );
        }
    }
}
