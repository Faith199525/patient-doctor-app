<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{

    protected $fillable = ['date','time','status'];

    protected $with = ['appointmentable'];
    
    public function appointmentable()
    {
        return $this->morphTo();
    }

}