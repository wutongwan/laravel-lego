<?php namespace Lego\Field\Provider;

use Lego\Field\Concerns\FilterOnly;
use Lego\Field\RangeField;

class NumberRange extends RangeField
{
    use FilterOnly;

    const RANGE_TYPE = Number::class;
}
