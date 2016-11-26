<?php namespace Lego\Field\Provider;

class TimeRange extends DatetimeRange
{
    protected $format = 'H:i:s';

    protected $inputType = 'time';

    protected $maxView = 'day';

    protected $startView = 'day';
}