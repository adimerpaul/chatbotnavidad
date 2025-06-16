<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorSchedule extends Model{
    use SoftDeletes;
    protected $fillable = [
        'doctor_id',
        'available_from',
        'available_to',
        'days',
        'price',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];
    function doctor(){
        return $this->belongsTo(Doctor::class);
    }
}
