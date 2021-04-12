<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    const ACTIVE = 'ACTIVE';
    const PENDING = 'PENDING';
    const COMPLETED = 'COMPLETED';

    protected $table= "diagnosis";

    protected $guarded = ['id'];

    protected $with = ['tests', 'partners', 'partners.partners'];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function partners()
    {
        return $this->belongsTo('App\Models\User', 'partners_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    public function payment()
    {
        return $this->morphOne('App\Models\Payment', 'paymentable');
    }
}
