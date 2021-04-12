<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionistService extends Model
{

    protected $fillable = ['patient_id','nutritionist_id','initial_complain','comment','status'];

    protected $with = ['patient', 'nutritionist'];

    public function patient()
    {
        return $this->belongsTo('App\Models\User','patient_id');
    }
    public function nutritionist()
    {
        return $this->belongsTo('App\Models\User','nutritionist_id');
    }

    public function payment()
    {
        return $this->morphOne('App\Models\Payment', 'paymentable');
    }
}
