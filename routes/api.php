<?php

use App\Models\Departamento;

Route::get('/destinos', function () {
    return Departamento::with('municipios:id,nombre,departamento_id')
        ->select('id', 'nombre')
        ->orderBy('nombre')
        ->get()
        ->map(function ($dep) {
            return [
                'id'=> $dep->id,
                'nombre' => mb_strtoupper($dep->nombre, 'UTF-8'),
                'municipios' => $dep->municipios->map(function ($mun) {
                    return [
                        'id'=> $mun->id,
                        'nombre' => mb_strtoupper($mun->nombre, 'UTF-8'),
                        'departamento_id' => $mun->departamento_id,
                    ];
                
                }),
            ];
        });
});
