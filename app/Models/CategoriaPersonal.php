<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaPersonal extends Model
{
    use HasFactory;

    protected $table = 'categorias_personal';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'categoria_personal_id');
    }

    public function obligaciones()
    {
        return $this->hasMany(ObligacionContrato::class, 'categoria_personal_id');
    }
}
