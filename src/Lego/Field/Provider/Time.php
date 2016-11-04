<?php namespace Lego\Field\Provider;

class Time extends Datetime
{
    protected $maxView = 'day';

    protected $startView = 'hour';

    protected function initialize()
    {
        parent::initialize();

        $this->format('H:i:s');
    }
}