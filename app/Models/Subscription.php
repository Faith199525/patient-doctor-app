<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = ['id'];
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    // public function transaction(){
    //     return $this->hasOne(Transaction::class);
    // }

    public function payment()
    {
        return $this->morphOne('App\Models\Payment', 'paymentable');
    }
}
