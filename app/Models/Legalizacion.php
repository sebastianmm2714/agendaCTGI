<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legalizacion extends Model
{
    use HasFactory;

    protected $table = 'legalizaciones';

    protected $fillable = [
        'agenda_desplazamiento_id',
        'codigo_regional',
        'codigo_centro',
        'gastos_transporte',
        'fotos',
        'planillas',
        'declaracion_path',
        'resultados',
        'compromisos',
        'conclusiones',
        'soportes_desplazamiento',
        'legalizado_at',
        'realiza_declaracion',
        'tiquetes',
        'estado',
        'observaciones',
        'firma_comisionado_path',
        'firma_supervisor_path',
        'firma_ordenador_path',
    ];

    protected $casts = [
        'gastos_transporte' => 'array',
        'fotos' => 'array',
        'planillas' => 'array',
        'resultados' => 'array',
        'compromisos' => 'array',
        'conclusiones' => 'array',
        'soportes_desplazamiento' => 'array',
        'legalizado_at' => 'datetime',
        'realiza_declaracion' => 'boolean',
        'tiquetes' => 'array',
    ];

    public function agenda()
    {
        return $this->belongsTo(AgendaDesplazamiento::class, 'agenda_desplazamiento_id');
    }
}
