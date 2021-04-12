<?php

namespace App\Models;

use App\Models\CaseFile;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed type
 * @property mixed partners_id
 */
class Referral extends Model
{
    const ACTIVE = 'ACTIVE';
    const ACCEPTED = 'ACCEPTED';

    protected $guarded = ['id'];

    public function setPriceInMinorUnitAttribute($value)
    {
        $this->attributes['price_in_minor_unit'] = $value * 100;
    }

    public function getPriceInMinorUnitAttribute($value)
    {
        return $value / 100;
    }

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function scopeDiagnostic($query)
    {
        return $query->where('type', 'DIAGNOSTIC');
    }

    public function scopeHospital($query)
    {
        return $query->where('type', 'HOSPITAL');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'ACCEPTED');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function appointment()
    {
       return $this->hasMany('App\Models\Appointment');
    }

    public function partners()
    {
       return $this->belongsTo('App\Models\User');
    }
}
