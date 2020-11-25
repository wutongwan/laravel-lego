<?php

namespace Lego\Input;

class Checkboxes extends Radios
{
    public function getInputType(): string
    {
        return 'checkbox';
    }

    public function isMultiSelect(): bool
    {
        return true;
    }
}
