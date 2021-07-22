<?php


namespace Miniyus\RestfulApiClient;


use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ApiClientServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/Api_server.php' => config_path('Api_server.php')
        ], 'config');
    }
}