<?php namespace Lego\Field\Provider;

class Date extends Datetime
{
    protected $minView = 'month';

    protected function initialize()
    {
        parent::initialize();

        $this->format('Y-m-d');
    }
}