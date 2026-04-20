<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaDesplazamiento extends Model
{
    use HasFactory;

    protected $table = 'agendas_desplazamiento';

    protected $fillable = [
        'user_id',
        'clasificacion_id',
        'estado_id',
        'fecha_elaboracion',
        'ruta',
        'entidad_empresa',
        'contacto',
        'objetivo_desplazamiento',
        'regional',
        'centro',
        'destinos',
        'fecha_inicio',
        'fecha_fin',
        'valor_viaticos',
        'observaciones_finanzas',
        'cdp',
        'valor_intermunicipal',
        'supervisor_id',
        'ordenador_id',
        'firma_contratista_path',
        'firma_supervisor_path',
        'firma_ordenador_path',
    ];
    
    protected $casts = [
        'fecha_elaboracion' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'destinos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clasificacion()
    {
        return $this->belongsTo(ClasificacionInformacion::class, 'clasificacion_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoAgenda::class, 'estado_id');
    }

    public function obligaciones()
    {
        return $this->belongsToMany(ObligacionContrato::class, 'agenda_obligaciones', 'agenda_id', 'obligacion_id');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'agenda_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Funcionario::class, 'supervisor_id');
    }

    public function ordenador()
    {
        return $this->belongsTo(Funcionario::class, 'ordenador_id');
    }
}