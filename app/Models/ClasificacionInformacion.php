<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClasificacionInformacion extends Model
{
    use HasFactory;

    protected $table = 'clasificacion_informacion';

    protected $fillable = [
        'nombre',
    ];

    public function agendas()
    {
        return $this->hasMany(AgendaDesplazamiento::class, 'clasificacion_id');
    }
}
