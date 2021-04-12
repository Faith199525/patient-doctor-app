<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DentalService extends Model
{
    protected $casts = [
        'service_details' => 'array'
    ];

    protected $fillable = ['patient_id','dentist_id','service_details'];

    protected $with = ['serviceCenter','patient'];

    public function patient()
    {
        return $this->belongsTo('App\Models\User','patient_id');    
    }

    public function serviceCenter()
    {
        return $this->belongsTo('App\Models\User','dentist_id');    
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
