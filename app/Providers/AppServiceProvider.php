<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app('validator')->extend('is_extension', function ($attribute, $value, $parameters, $validator) {
            $objectFile = app('request')->file($attribute);
            if (!empty($objectFile)) {
                $file_extension = $objectFile->getClientOriginalExtension();
                return in_array($file_extension, $parameters);
            }
            return false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
