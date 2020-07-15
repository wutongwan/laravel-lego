<?php

namespace Lego\Widget\Grid;

use Illuminate\Support\Str;

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

    public function handleLimit($limit = 100)
    {
        return Str::limit($this->value(), $limit);
    }
}
