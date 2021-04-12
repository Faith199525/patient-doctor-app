<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    protected $casts = [
        'medical_info' => 'array'
    ];

    protected $fillable = ['patient_id','medical_info','additional_info'];

    public function user()
    {
        return $this->belongsTo('App\Models\User','patient_id');    
    }
}
