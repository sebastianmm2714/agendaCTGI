<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipios';

    protected $fillable = [
        'nombre',
        'departamento_id'
    ];

    public $timestamps = false;

    // 🔗 Un municipio pertenece a un departamento
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}
