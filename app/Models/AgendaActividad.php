<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaActividad extends Model
{
    use HasFactory;

    protected $table = 'agenda_actividades';

    protected $fillable = [
        'agenda_desplazamiento_id',
        'fecha_reporte',
        'ruta_ida',
        'ruta_regreso',
        'transporte_ida',
        'transporte_regreso',
        'medios_transporte', // Keep for compatibility if needed
        'actividades_ejecutar',
        'desplazamientos_internos',
        'valor_aereo',
        'valor_terrestre',
        'valor_intermunicipal',
        'observaciones',
    ];

    protected $casts = [
        'transporte_ida' => 'array',
        'transporte_regreso' => 'array',
        'medios_transporte' => 'array',
        'actividades_ejecutar' => 'array',
        'fecha_reporte' => 'date',
    ];

    /* ================= RELACIONES ================= */

    public function agenda()
    {
        return $this->belongsTo(AgendaDesplazamiento::class);
    }
}
