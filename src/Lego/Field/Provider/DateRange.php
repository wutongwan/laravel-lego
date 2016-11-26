<?php namespace Lego\Field\Provider;

class DateRange extends DatetimeRange
{
    protected $format = 'Y-m-d';

    protected $inputType = 'date';

    protected $minView = 'month';
}