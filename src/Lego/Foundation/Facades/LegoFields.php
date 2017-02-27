<?php namespace Lego\Foundation\Facades;

use Illuminate\Support\Facades\Facade;

class LegoFields extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lego-fields';
    }
}
