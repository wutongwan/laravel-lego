<?php

namespace Lego\Field\Concerns;

use Lego\Foundation\Exceptions\LegoException;

trait FilterOnly
{
    public function syncValueToSource()
    {
        throw new LegoException(static::class . ' for filter only.');
    }
}
