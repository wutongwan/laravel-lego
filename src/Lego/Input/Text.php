<?php

namespace Lego\Input;

use Lego\Contracts\Input\FilterInput;
use Lego\Contracts\Input\FormInput;
use Lego\Input\Filter\TextFilterHandler;
use Lego\Input\Form\TextHandler;

class Text extends Input implements FormInput, FilterInput
{
    public function getInputType(): string
    {
        return 'text';
    }

    protected function viewName(): string
    {
        return 'lego::bootstrap3.input.text';
    }

    public function render()
    {
        return ($view = $this->viewName())
            ? view($view, ['input' => $this])
            : '';
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
