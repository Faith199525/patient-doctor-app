<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralPractitionerService extends Model
{
    protected $fillable = ['patient_id','gp_id','complaint','no_of_days','start_date','comment'];

    protected $with = ['serviceCenter','patient'];

    public function patient()
    {
        return $this->belongsTo('App\Models\User','patient_id');    
    }

    public function serviceCenter()
    {
        return $this->belongsTo('App\Models\User','gp_id');    
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
