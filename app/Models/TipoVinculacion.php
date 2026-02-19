<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVinculacion extends Model
{
    protected $table = 'tipos_vinculacion';
    protected $fillable = ['nombre'];

    public function contratistas()
    {
        return $this->hasMany(Contratista::class);
    }
}
