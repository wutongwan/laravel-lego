<?php namespace Lego\Field\Operators;

use Lego\LegoException;

trait ForFilterOnly
{
    public function syncCurrentValueToSource()
    {
        throw new LegoException(static::class . ' for filter only.');
    }
}