<?php

namespace Lego\Foundation\Facades;

use Illuminate\Support\Facades\Facade;

class LegoAssets extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lego-assets';
    }
}
