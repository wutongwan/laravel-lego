<?php

namespace Lego\Field\Concerns;

use Illuminate\Support\Facades\Config;

trait HasConfig
{
    protected function config($key, $default = null)
    {
        return Config::get('lego.field.provider.' . static::class . '.' . $key, $default);
    }
}
