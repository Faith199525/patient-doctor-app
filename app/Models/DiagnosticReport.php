<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticReport extends Model
{

	protected $table="diagnostic_reports";

    protected $guarded = ['deleted_at'];
    protected $hidden=['deleted_at'];

   

    // public function files()
    // {
    //     return $this->morphMany('App\Models\File', 'fileable');
    // }

    // public function appointment()
    // {
    //    return $this->belongsTo('App\Models\Appointment');
    // }

}
