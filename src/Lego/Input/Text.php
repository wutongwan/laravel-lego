<?php

namespace Lego\Input;

use Lego\Contracts\Input\FilterInput;
use Lego\Contracts\Input\FormInput;
use Lego\Input\Filter\TextFilterHandler;
use Lego\Input\Form\TextHandler;
use Lego\Utility\HtmlUtility;

class Text extends Input implements FormInput, FilterInput
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
            [
                'class' => 'form-control',
                'placeholder' => $this->getPlaceholder(),
            ]
        );
    }

    public function formInputHandler()
    {
        return TextHandler::class;
    }

    public function filterInputHandler()
    {
        return TextFilterHandler::class;
    }
}
