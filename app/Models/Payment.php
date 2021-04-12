<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $casts = [
        'payment_details' => 'array'
    ];
    
    protected $fillable = ['payment_details','status'];

    public function paymentable()
    {
        return $this->morphTo();
    }
}
