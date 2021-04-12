<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerBranch extends Model
{
	protected $table="partner_branches";

    protected $guarded = ['id'];

    public function partner()
    {
       return $this->belongsTo('App\Models\Partners');
    }
}
