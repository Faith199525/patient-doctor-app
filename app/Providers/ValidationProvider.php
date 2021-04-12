<?php

namespace App\Providers;


use App\Rules\ExistColumnRule;
use App\Rules\GenderStatusRule;
use App\Rules\UniqueColumnRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;


class ValidationProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('gender', function ($attribute, $value) {
            return (new GenderStatusRule())->passes($attribute, $value);
        });

        Validator::extend('unique_column', function ($attribute, $value, $parameters) {
            return (new UniqueColumnRule())->passes($attribute, $value, $parameters);
        });

        Validator::extend('exist_column', function ($attribute, $value, $parameters) {
            return (new ExistColumnRule())->passes($attribute, $value, $parameters);
        });

    }
}
