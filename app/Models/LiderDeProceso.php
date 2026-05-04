<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiderDeProceso extends Model
{
    use HasFactory;

    protected $table = 'lideres_de_proceso';

    protected $fillable = [
        'nombre',
        'email',
        'tipo_documento',
        'numero_documento',
        'cargo',
        'tipo',
        'numero_cuenta_tipo',
        'firma',
    ];

    public function usuariosSupervisor()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    public function usuariosOrdenador()
    {
        return $this->hasMany(User::class, 'ordenador_id');
    }

    public function agendas()
    {
        return $this->hasMany(AgendaDesplazamiento::class, 'supervisor_id');
    }
}
