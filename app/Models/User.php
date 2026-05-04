<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_documento',
        'numero_documento',
        'numero_contrato',
        'anio_contrato',
        'fecha_vencimiento',
        'objeto_contractual',
        'firma',
        'salario_honorarios',
        'numero_cuenta_tipo',
        'categoria_personal_id',
        'supervisor_id',
        'ordenador_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'salario_honorarios' => 'decimal:2',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaPersonal::class, 'categoria_personal_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(LiderDeProceso::class, 'supervisor_id');
    }

    public function ordenador()
    {
        return $this->belongsTo(LiderDeProceso::class, 'ordenador_id');
    }

    public function agendas()
    {
        return $this->hasMany(AgendaDesplazamiento::class);
    }
}
