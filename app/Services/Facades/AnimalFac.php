<?php namespace Horses\Services\Facades;

use Illuminate\Support\Facades\Facade;

class AnimalFac extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'animalsev';
    }
}