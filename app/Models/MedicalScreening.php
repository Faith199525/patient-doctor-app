<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalScreening extends Model
{
    protected $casts = [
        'tests' => 'array'
    ];

    protected $fillable = ['patient_id','diagnostic_id','tests'];

    protected $with = ['serviceCenter','patient'];

    public function patient()
    {
        return $this->belongsTo('App\Models\User','patient_id');    
    }

    public function serviceCenter()
    {
        return $this->belongsTo('App\Models\User','diagnostic_id');    
    }

    public function appointment()
    {
        return $this->morphOne('App\Models\Appointment', 'appointmentable');
    }

    public function payment()
    {
        return $this->morphOne('App\Models\Payment', 'paymentable');
    }
}
