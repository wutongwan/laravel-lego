<?php namespace Lego\Field\Operators;

use Lego\LegoException;

trait FilterOnly
{
    public function syncValueToSource()
    {
        throw new LegoException(static::class . ' for filter only.');
    }
}
