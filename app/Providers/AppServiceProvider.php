<?php namespace Horses\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->singleton('animalsev', function () {
            return new \Horses\Services\AnimalService;
        });
        $this->app->singleton('categoryserv', function () {
            return new \Horses\Services\CategoryService;
        });
    }

    public function provides()
    {
        return ['Horses\Services\AnimalService', 'Horses\Services\CategoryService'];
    }

}
