<?php namespace Horses\Services\Facades;

use Illuminate\Support\Facades\Facade;

class CategoryFac extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'categoryserv';
    }
}