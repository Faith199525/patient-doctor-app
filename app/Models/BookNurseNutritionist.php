<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookNurseNutritionist extends Model
{
    const PENDING = 'PENDING';
    const ACCEPTED = 'ACCEPTED';
    const COMPLETED = 'COMPLETED';

    const NURSE = 'NURSE';
    const NUTRITIONIST = 'NUTRITIONIST';
    
    protected $table= "book_nurse_nutritionists";

    protected $guarded = ['deleted_at'];
    protected $hidden=['deleted_at'];

    public function patient()
    {
       return $this->belongsTo('App\Models\User');
    }

    public function partner()
    {
       return $this->belongsTo('App\Models\User');
    }
}