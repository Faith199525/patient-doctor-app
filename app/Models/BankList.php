<?php

namespace App\Models;

use App\Models\Enums\GenericStatusConstant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BankList extends Model
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', '=', GenericStatusConstant::ACTIVE);
        });
    }
}
