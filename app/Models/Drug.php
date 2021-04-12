<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $guarded = ['id'];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
