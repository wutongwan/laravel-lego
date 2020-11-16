<?php

namespace Lego\Input;

use Lego\Contracts\RenderAble;
use Lego\Utility\HtmlUtility;

class Text extends Input implements RenderAble
{
    protected function inputType(): string
    {
        return 'text';
    }

    public function render(): string
    {
        return HtmlUtility::input(
            $this->inputType(),
            $this->getInputName(),
            $this->values()->getCurrentValue(),
            ['class' => 'form-control']
        );
    }
}
