<?php

namespace Database\Seeders;

use App\Models\TipoVinculacion;
use Illuminate\Database\Seeder;

class TipoVinculacionSeeder extends Seeder
{
    
    public function run(): void
    {
        $tipos = [
            'INSTRUCTOR',
            'APOYOS ADM Y GEST'
        ];

        foreach ($tipos as $tipo) {
            TipoVinculacion::firstOrCreate(['nombre' => $tipo]);
        }
    }
}
