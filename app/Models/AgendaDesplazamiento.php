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
        'orden_viaje',
        'legalizacion_codigo_regional',
        'legalizacion_codigo_centro',
        'legalizacion_gastos_transporte',
        'legalizacion_fotos',
        'legalizacion_planillas',
        'legalizacion_declaracion',
        'legalizacion_resultados',
        'legalizacion_compromisos',
        'legalizacion_conclusiones',
        'legalizacion_soportes_desplazamiento',
        'legalizado_at',
        'realiza_declaracion',
        'legalizacion_tiquetes',
        'legalizacion_estado',
        'legalizacion_observaciones',
        'legalizacion_firma_comisionado_path',
        'legalizacion_firma_supervisor_path',
        'legalizacion_firma_ordenador_path',
    ];
    
    protected $casts = [
        'fecha_elaboracion' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'destinos' => 'array',
        'legalizacion_fotos' => 'array',
        'legalizacion_planillas' => 'array',
        'legalizacion_resultados' => 'array',
        'legalizacion_compromisos' => 'array',
        'legalizacion_conclusiones' => 'array',
        'legalizacion_soportes_desplazamiento' => 'array',
        'legalizacion_gastos_transporte' => 'array',
        'legalizado_at' => 'datetime',
        'realiza_declaracion' => 'boolean',
        'legalizacion_tiquetes' => 'array',
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
        return $this->belongsTo(LiderDeProceso::class, 'supervisor_id');
    }

    public function ordenador()
    {
        return $this->belongsTo(LiderDeProceso::class, 'ordenador_id');
    }

    public function legalizacion()
    {
        return $this->hasOne(Legalizacion::class, 'agenda_desplazamiento_id');
    }

    // --- CAPA DE COMPATIBILIDAD (ACCESORES Y MUTADORES DE LEGALIZACION) ---

    public function getLegalizacionCodigoRegionalAttribute()
    {
        return $this->legalizacion ? $this->legalizacion->codigo_regional : ($this->attributes['legalizacion_codigo_regional'] ?? null);
    }

    public function setLegalizacionCodigoRegionalAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->codigo_regional = $value;
        $legalizacion->save();
    }

    public function getLegalizacionCodigoCentroAttribute()
    {
        return $this->legalizacion ? $this->legalizacion->codigo_centro : ($this->attributes['legalizacion_codigo_centro'] ?? null);
    }

    public function setLegalizacionCodigoCentroAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->codigo_centro = $value;
        $legalizacion->save();
    }

    public function getLegalizacionGastosTransporteAttribute()
    {
        if ($this->legalizacion) {
            return $this->legalizacion->gastos_transporte;
        }
        $raw = $this->attributes['legalizacion_gastos_transporte'] ?? null;
        return is_string($raw) ? json_decode($raw, true) : ($raw ?: []);
    }

    public function setLegalizacionGastosTransporteAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->gastos_transporte = $value;
        $legalizacion->save();
    }

    public function getLegalizacionFotosAttribute()
    {
        if ($this->legalizacion) {
            return $this->legalizacion->fotos;
        }
        $raw = $this->attributes['legalizacion_fotos'] ?? null;
        return is_string($raw) ? json_decode($raw, true) : ($raw ?: []);
    }

    public function setLegalizacionFotosAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->fotos = $value;
        $legalizacion->save();
    }

    public function getLegalizacionPlanillasAttribute()
    {
        if ($this->legalizacion) {
            return $this->legalizacion->planillas;
        }
        $raw = $this->attributes['legalizacion_planillas'] ?? null;
        return is_string($raw) ? json_decode($raw, true) : ($raw ?: []);
    }

    public function setLegalizacionPlanillasAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->planillas = $value;
        $legalizacion->save();
    }

    public function getLegalizacionDeclaracionAttribute()
    {
        return $this->legalizacion ? $this->legalizacion->declaracion_path : ($this->attributes['legalizacion_declaracion'] ?? null);
    }

    public function setLegalizacionDeclaracionAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->declaracion_path = $value;
        $legalizacion->save();
    }

    public function getLegalizacionResultadosAttribute()
    {
        if ($this->legalizacion) {
            return $this->legalizacion->resultados;
        }
        $raw = $this->attributes['legalizacion_resultados'] ?? null;
        return is_string($raw) ? json_decode($raw, true) : ($raw ?: []);
    }

    public function setLegalizacionResultadosAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->resultados = $value;
        $legalizacion->save();
    }

    public function getLegalizacionCompromisosAttribute()
    {
        if ($this->legalizacion) {
            return $this->legalizacion->compromisos;
        }
        $raw = $this->attributes['legalizacion_compromisos'] ?? null;
        return is_string($raw) ? json_decode($raw, true) : ($raw ?: []);
    }

    public function setLegalizacionCompromisosAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->compromisos = $value;
        $legalizacion->save();
    }

    public function getLegalizacionConclusionesAttribute()
    {
        if ($this->legalizacion) {
            return $this->legalizacion->conclusiones;
        }
        $raw = $this->attributes['legalizacion_conclusiones'] ?? null;
        return is_string($raw) ? json_decode($raw, true) : ($raw ?: []);
    }

    public function setLegalizacionConclusionesAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->conclusiones = $value;
        $legalizacion->save();
    }

    public function getLegalizadoAtAttribute($value)
    {
        if ($this->legalizacion) {
            return $this->legalizacion->legalizado_at;
        }
        return $value ? $this->asDateTime($value) : null;
    }

    public function setLegalizadoAtAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->legalizado_at = $value;
        $legalizacion->save();
    }

    public function getRealizaDeclaracionAttribute()
    {
        if ($this->legalizacion) {
            return (bool)$this->legalizacion->realiza_declaracion;
        }
        return isset($this->attributes['realiza_declaracion']) ? (bool)$this->attributes['realiza_declaracion'] : false;
    }

    public function setRealizaDeclaracionAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->realiza_declaracion = (bool)$value;
        $legalizacion->save();
    }

    public function getLegalizacionTiquetesAttribute()
    {
        if ($this->legalizacion) {
            return $this->legalizacion->tiquetes;
        }
        $raw = $this->attributes['legalizacion_tiquetes'] ?? null;
        return is_string($raw) ? json_decode($raw, true) : ($raw ?: []);
    }

    public function setLegalizacionTiquetesAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->tiquetes = $value;
        $legalizacion->save();
    }

    public function getLegalizacionEstadoAttribute()
    {
        return $this->legalizacion ? $this->legalizacion->estado : ($this->attributes['legalizacion_estado'] ?? null);
    }

    public function setLegalizacionEstadoAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->estado = $value;
        $legalizacion->save();
    }

    public function getLegalizacionObservacionesAttribute()
    {
        return $this->legalizacion ? $this->legalizacion->observaciones : ($this->attributes['legalizacion_observaciones'] ?? null);
    }

    public function setLegalizacionObservacionesAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->observaciones = $value;
        $legalizacion->save();
    }

    public function getLegalizacionFirmaComisionadoPathAttribute()
    {
        return $this->legalizacion ? $this->legalizacion->firma_comisionado_path : null;
    }

    public function setLegalizacionFirmaComisionadoPathAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->firma_comisionado_path = $value;
        $legalizacion->save();
    }

    public function getLegalizacionFirmaSupervisorPathAttribute()
    {
        return $this->legalizacion ? $this->legalizacion->firma_supervisor_path : null;
    }

    public function setLegalizacionFirmaSupervisorPathAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->firma_supervisor_path = $value;
        $legalizacion->save();
    }

    public function getLegalizacionFirmaOrdenadorPathAttribute()
    {
        return $this->legalizacion ? $this->legalizacion->firma_ordenador_path : null;
    }

    public function setLegalizacionFirmaOrdenadorPathAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->firma_ordenador_path = $value;
        $legalizacion->save();
    }

    public function getLegalizacionSoportesDesplazamientoAttribute()
    {
        if ($this->legalizacion) {
            return $this->legalizacion->soportes_desplazamiento;
        }
        $raw = $this->attributes['legalizacion_soportes_desplazamiento'] ?? null;
        return is_string($raw) ? json_decode($raw, true) : ($raw ?: []);
    }

    public function setLegalizacionSoportesDesplazamientoAttribute($value)
    {
        $legalizacion = $this->legalizacion()->firstOrCreate([], ['estado' => 'CREADA']);
        $legalizacion->soportes_desplazamiento = $value;
        $legalizacion->save();
    }
}