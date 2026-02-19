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
        'nombre_completo',
        'tipo_documento',
        'numero_documento',
        'cargo',

        'numero_contrato',
        'anio_contrato',
        'fecha_elaboracion',
        'fecha_vencimiento',
        'clasificacion_informacion',
        'objetivo_contractual',

        'ruta',
        'ciudad_destino',
        'entidad_empresa',
        'contacto',
        'fecha_inicio_desplazamiento',
        'fecha_fin_desplazamiento',
        'objetivo_desplazamiento',

        'direccion_general',
        'dependencia_centro',

        'obligaciones_contrato',

        'firma_contratista',
        'firma_supervisor',
        'firma_ordenador',

        'estado'
    ];

    protected $casts = [
        'obligaciones_contrato' => 'array',
    ];


    public function actividades()
    {
        return $this->hasMany(AgendaActividad::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }



}
