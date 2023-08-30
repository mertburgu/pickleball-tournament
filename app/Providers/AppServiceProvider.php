<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('player_limit_multiple', function ($attribute, $value, $parameters, $validator) {
            $format = $parameters[0];

            if ($format === '0') {
                return $value > 2;
            } elseif ($format === '1') {
                return $value % 2 === 0;
            }
            return false;
        });

    }
}
