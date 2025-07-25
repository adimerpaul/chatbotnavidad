<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pregunta extends Model{
    use SoftDeletes;

    protected $fillable = [
        'pregunta',
        'respuesta',
        'activo',
        'precio'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
