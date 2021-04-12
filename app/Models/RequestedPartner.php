<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestedPartner extends Model
{

    const AMBULANCE = 'AMBULANCE';
    const NURSE = 'NURSE';
    const NUTRITIONIST = 'NUTRITIONIST';

    const SAVINGS = 'SAVINGS';
    const CURRENT = 'CURRENT';
    
    protected $table="requested_partners";

    protected $guarded = ['id'];
    protected $hidden=['id'];

    public function user()
    {
       return $this->belongsTo('App\Models\User');
    }

    public function certificates()
    {
        return $this->hasMany(RequestedPartnersCertificate::class);
    }

}
