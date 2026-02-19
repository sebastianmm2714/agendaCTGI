<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        User::updateOrCreate(
            ['email' => 'contratista@sena.edu.co'],
            [
                'name' => 'Contratista',
                'password' => Hash::make('12345678'),
                'rol' => 'contratista'
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@sena.edu.co'],
            [
                'name' => 'Supervisor Contrato',
                'password' => Hash::make('12345678'),
                'rol' => 'supervisor_contrato'
            ]
        );

        User::updateOrCreate(
            ['email' => 'ordenador@sena.edu.co'],
            [
                'name' => 'Ordenador Gasto',
                'password' => Hash::make('12345678'),
                'rol' => 'ordenador_gasto'
            ]
        );

        User::updateOrCreate(
            ['email' => 'viaticos@sena.edu.co'],
            [
                'name' => 'Viaticos',
                'password' => Hash::make('12345678'),
                'rol' => 'viaticos'
            ]
        );
    }
}
