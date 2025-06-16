<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'specialty',
        'phone',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];
    function schedules(){
        return $this->hasMany(DoctorSchedule::class);
    }
}
