<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    const ACTIVE = 'ACTIVE';
    const ACCEPTED = 'ACCEPTED';
    const APPROVED = 'APPROVED';
    const PENDING = 'PENDING';
    const DECLINED = 'DECLINED';
    const REJECTED = 'REJECTED';


    protected $guarded = ['id'];

    protected $with = ['drugs', 'partners','partners.partners'];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function partners()
    {
        return $this->belongsTo('App\Models\User', 'partners_id');
    }

    public function drugs()
    {
        return $this->hasMany(Drug::class);
    }

    public function payment()
    {
        return $this->morphOne('App\Models\Payment', 'paymentable');
    }
}
