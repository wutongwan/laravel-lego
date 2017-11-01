<?php namespace Lego\Field\Provider;

class Time extends Datetime
{
    protected $format = 'H:i:s';

    protected $inputType = 'time';

    protected $maxView = 'day';

    protected $startView = 'day';

    protected function initialize()
    {
        parent::initialize();

        $this->removeRule('date');
    }
}
