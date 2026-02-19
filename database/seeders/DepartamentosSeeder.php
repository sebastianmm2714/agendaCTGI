<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\support\Facades\DB;

class DepartamentosSeeder extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(database_path('data/Departamentos_y_municipios_de_Colombia.csv')));
        array_shift($rows);

        $departamentos = [];

        foreach ($rows as $row) {
            $codigo = trim($row[1]);
            $nombre = trim($row[2]);

            $departamentos[$codigo] = [
                'codigo_dane' => $codigo,
                'nombre' => $nombre,
            ];
        }
        DB::table('departamentos')->insert(array_values($departamentos));
    }
}
