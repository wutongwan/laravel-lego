<?php namespace Lego\Field\Provider;

class Time extends Datetime
{
    protected $inputType = 'time';

    protected $maxView = 'day';

    protected $startView = 'day';

    protected function initialize()
    {
        parent::initialize();

        $this->format('H:i:s');
    }
}