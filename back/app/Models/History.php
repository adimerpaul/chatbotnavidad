<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';

    protected $fillable = [
        'ref',
        'keyword',
        'answer',
        'refSerialize',
        'phone',
        'options',
        'created_at',
    ];

    public $timestamps = false;
    public function atencionManual(){
        return $this->belongsTo(AtencionManual::class, 'phone', 'phone');
    }
}
