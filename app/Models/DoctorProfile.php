<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    protected $guarded = ['id'];
    protected $with = ['specialty'];

    public function medicalCertificates()
    {
        return $this->hasMany(MedicalCertificate::class);
    }

    public function specialty()
    {
        return $this->belongsTo('App\Models\Specialty');
    }

    public function branches()
    {
        return $this->hasMany(SpeciatyBranch::class);
    }
}
