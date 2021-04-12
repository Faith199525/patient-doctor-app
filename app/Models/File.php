<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';
    protected $guarded = ['deleted_at'];
    protected $hidden = [ 'created_at', 'updated_at', 'deleted_at'];

    public function fileable()
    {
        return $this->morphTo();
    }
}
