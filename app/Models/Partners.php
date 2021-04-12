<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed name
 * @property mixed address
 * @property mixed license_number
 * @property mixed email
 * @property mixed phone_number
 * @property mixed description
 * @property mixed logo
 * @property mixed type
 */
class Partners extends Model
{

    use Notifiable;

    protected $fillable = ['name','address' , 'license_number' , 'email' , 'phone_number','description', 'status','type','account_name','account_number','bank_name','working_days','start_time','closing_time','representative_one_name','representative_one_email','representative_one_phone_number','representative_two_name','representative_two_email','representative_two_phone_number'];

    const DIAGNOSTIC = 'DIAGNOSTIC';
    const HOSPITAL = 'HOSPITAL';
    const PHARMACY = 'PHARMACY';

    public function members()
    {
        return $this->belongsToMany(User::class, 'partner_members');
    }

    public function scopeMember($query, $userId)
    {
        return $query->whereHas('members', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }

    public function appointments()
    {
       return $this->hasMany('App\Models\Appointments');
    }

    public function callup()
    {
       return $this->belongsTo('App\Models\Callup');
    }

    public function bookNurse()
    {
       return $this->hasMany('App\Models\BookNurse');
    }

    public function branches()
    {
        return $this->hasMany(PartnerBranch::class);
    }

}
