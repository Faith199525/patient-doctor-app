<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionChat extends Model
{
    protected $fillable = ['sender', 'active', 'body', 'nutritionist_service_id'];
}
