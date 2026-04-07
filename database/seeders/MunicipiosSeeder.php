<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipiosSeeder extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(database_path('data/Departamentos_y_municipios_de_Colombia.csv')));
        array_shift($rows);

        foreach ($rows as $row) {
            $departamento = DB::table('departamentos')
                ->where('codigo_dane', trim($row[1]))
                ->first();

            if ($departamento) {
                DB::table('municipios')->updateOrInsert(
                ['codigo_dane' => trim($row[3])],
                [
                    'departamento_id' => $departamento->id,
                    'nombre' => trim($row[4]),
                    'updated_at' => now(),
                    'created_at' => now()
                ]
                );
            }
        }
    }
}
