<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    protected $fillable = [
        'agenda_id',
        'fecha',
        'ruta_ida',
        'ruta_regreso',
        'transporte_ida',
        'transporte_regreso',
        'actividad',
        'valor_aereo',
        'valor_terrestre',
        'valor_intermunicipal',
    ];

    protected $casts = [
        'transporte_ida' => 'array',
        'transporte_regreso' => 'array',
        'actividad' => 'array',
        'fecha' => 'date',
    ];

    public function agenda()
    {
        return $this->belongsTo(AgendaDesplazamiento::class, 'agenda_id');
    }
}
