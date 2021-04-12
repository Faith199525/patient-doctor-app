<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Callup extends Model{

    const PENDING = 'PENDING';
    const ACCEPTED = 'ACCEPTED';
    const COMPLETED = 'COMPLETED';
    const DECLINED = 'DECLINED';
    
    protected $table="ambulance_call_ups";

    protected $guarded = ['id'];
    protected $hidden=['deleted_at'];

    protected $with = ['user', 'ambulance'];

    public function user()
    {
       return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function ambulance()
    {
       return $this->belongsTo('App\Models\User', 'ambulance_id');
    }

}