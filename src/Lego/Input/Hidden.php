<?php

namespace Lego\Input;

class Hidden extends Text
{
    protected function inputType(): string
    {
        return 'hidden';
    }
}
