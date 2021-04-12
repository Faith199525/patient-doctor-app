<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{

	protected $table= "test";
    protected $guarded = ['id'];

    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class);
    }
}
