<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'tipo_documento',
        'numero_documento',
        'cargo',
        'tipo',
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
}
