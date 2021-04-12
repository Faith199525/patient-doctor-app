<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookNurse extends Model
{
    
    const ACTIVE = 'ACTIVE';
    const COMPLETED = 'COMPLETED';
    const CONFIRMED = 'CONFIRMED';
    
    protected $table= "book_nurses";

    protected $guarded = ['id'];
    protected $hidden=['deleted_at'];

    protected $with = ['patient', 'serviceCenter'];

    public function patient()
    {
       return $this->belongsTo('App\Models\User','patient_id');
    }

    public function serviceCenter()
    {
       return $this->belongsTo('App\Models\User', 'partner_id');
    }

    public function payment()
    {
        return $this->morphOne('App\Models\Payment', 'paymentable');
    }

    public function appointment()
    {
        return $this->morphOne('App\Models\Appointment', 'appointmentable');
    }
}