<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model{
    use SoftDeletes;
    protected $fillable = [
        'doctor_id',
        'cliente',
        'fecha_inicio',
        'fecha_fin',
        'observacion',
        'estado'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
