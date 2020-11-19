<?php

namespace Lego\ModelAdaptor\Native;

use Illuminate\Support\Arr;

class ArrayAdaptor extends StdClassAdaptor
{
    protected function get($data, $key)
    {
        return Arr::get($data, $key);
    }

    protected function set(&$data, $key, $value): void
    {
        Arr::set($data, $key, $value);
    }
}
