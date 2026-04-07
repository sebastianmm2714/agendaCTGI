<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObligacionContrato extends Model
{
    use HasFactory;

    protected $table = 'obligaciones_contrato';

    protected $fillable = [
        'categoria_personal_id',
        'nombre',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaPersonal::class, 'categoria_personal_id');
    }

    public function agendas()
    {
        return $this->belongsToMany(AgendaDesplazamiento::class, 'agenda_obligaciones', 'obligacion_id', 'agenda_id');
    }
}
