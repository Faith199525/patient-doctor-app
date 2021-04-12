<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpticalService extends Model
{
    protected $casts = [
        'service_details' => 'array'
    ];

    protected $fillable = ['patient_id','optician_id','service_details'];

    protected $with = ['serviceCenter','patient'];

    public function patient()
    {
        return $this->belongsTo('App\Models\User','patient_id');    
    }

    public function serviceCenter()
    {
        return $this->belongsTo('App\Models\User','optician_id');    
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
