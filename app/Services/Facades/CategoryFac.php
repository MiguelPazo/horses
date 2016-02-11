<?php namespace Horses\Services\Facades;

use Barryvdh\Debugbar\Facade;

class CategoryFac extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'categoryserv';
    }
}