<?php

namespace Lego\Input;

use Lego\Contracts\Input\HiddenInput;

class Hidden extends Text implements HiddenInput
{
    protected function inputType(): string
    {
        return 'hidden';
    }
}
