<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseFile extends Model
{
    protected $guarded = ['id'];

    protected $with = ['patient', 'doctor', 'prescription', 'prescription.partners', 'diagnosis', 'diagnosis.partners'];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id', 'id');
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class, 'case_file_id', 'id');
    }

    public function diagnosis()
    {
        return $this->hasOne(Diagnosis::class, 'case_file_id', 'id');
    }

    public function payment()
    {
        return $this->morphOne('App\Models\Payment', 'paymentable');
    }

}
