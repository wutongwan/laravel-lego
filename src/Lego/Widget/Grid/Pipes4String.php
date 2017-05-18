<?php namespace Lego\Widget\Grid;

class Pipes4String extends Pipes
{
    public function handleTrim()
    {
        return trim($this->value());
    }

    public function handleStrip()
    {
        return strip_tags($this->value());
    }
}
