<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtencionManual extends Model{
    use SoftDeletes;

    protected $table = 'atencion_manual';

    protected $fillable = [
        'phone',
        'hora_atencion',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public function history(){
        return $this->hasMany(History::class, 'phone', 'phone');
    }
}
