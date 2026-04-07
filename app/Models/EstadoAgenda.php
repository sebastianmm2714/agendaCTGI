<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoAgenda extends Model
{
    use HasFactory;

    protected $table = 'estados_agenda';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function agendas()
    {
        return $this->hasMany(AgendaDesplazamiento::class, 'estado_id');
    }
}
