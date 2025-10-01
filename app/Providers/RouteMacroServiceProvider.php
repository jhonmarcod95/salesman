<?php

namespace App\Providers;

use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;


class RouteMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Route::macro('description', function (string $text) {
            $this->action['description'] = $text;
            return $this;
        });

        // PAGE // RESOURCE // ACTION // SYSTEM
        Route::macro('purpose', function (string $text) {
            $this->action['purpose'] = $text;
            return $this;
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}