<?php namespace Lego\Field\Provider;

class Hidden extends Text
{
    protected $inputType = 'hidden';

    protected function initialize()
    {
        parent::initialize();

        $this->container('class', 'hide');
    }
}
