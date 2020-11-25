<?php

namespace Lego\Input;

use Lego\Contracts\Input\HiddenInput;

class Hidden extends Text implements HiddenInput
{
    public function getInputType(): string
    {
        return 'hidden';
    }
}
