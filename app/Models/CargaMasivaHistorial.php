<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargaMasivaHistorial extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_archivo',
        'ruta_reporte',
        'total_registros',
        'total_exito',
        'total_errores',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
