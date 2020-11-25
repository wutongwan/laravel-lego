<?php

namespace Lego\Input;

use Lego\Input\Form\NumberHandler;

class Number extends Text
{
    public function formInputHandler()
    {
        return NumberHandler::class;
    }

    public function min($value)
    {
        $this->attributes()->set('min', $value);
        return $this;
    }

    public function max($value)
    {
        $this->attributes()->set('max', $value);
        return $this;
    }

    public function step($value)
    {
        $this->attributes()->set('step', $value);
        return $this;
    }

    public function getInputType(): string
    {
        return is_integer($this->attributes()->get('step')) ? 'number' : 'text';
    }
}
