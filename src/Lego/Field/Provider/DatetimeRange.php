<?php namespace Lego\Field\Provider;

use Lego\Field\Concerns\FilterOnly;
use Lego\Field\RangeField;

class DatetimeRange extends RangeField
{
    use FilterOnly;

    const RANGE_TYPE = Datetime::class;
}
