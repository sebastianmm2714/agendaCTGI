<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Contratista extends Model
{
    protected $fillable = [
        'cedula',
        'nombre',
        'numero_cuenta',
        'tipo_vinculacion_id'
    ];

    public function tipoVinculacion()
    {
        return $this->belongsTo(TipoVinculacion::class);
    }
}
