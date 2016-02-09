<?php namespace Horses\Services\Facades;

use Barryvdh\Debugbar\Facade;

class AnimalFac extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'animalsev';
    }
}