<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to redirect users after login.
     */
    public const HOME = '/admin/dashboard'; // ✅ Redirect users here after login

    public function boot()
    {
        //
    }
}