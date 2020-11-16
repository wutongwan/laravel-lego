<?php

namespace Lego\Input;

use Lego\Utility\HtmlUtility;

class Text extends Input
{
    protected function inputType(): string
    {
        return 'text';
    }

    public function render()
    {
        return HtmlUtility::input(
            $this->inputType(),
            $this->getInputName(),
            $this->values()->getCurrentValue(),
            ['class' => 'form-control']
        );
    }
}
