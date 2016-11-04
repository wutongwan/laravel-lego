<?php namespace Lego\Field\Provider;

class Datetime extends Date
{
    protected function initialize()
    {
        parent::initialize();

        $this->format('Y-m-d H:i:s');
    }
}