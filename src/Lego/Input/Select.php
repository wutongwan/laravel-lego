<?php

declare(strict_types=1);

namespace Lego\Input;

use Lego\Input\Form\SelectHandler;

class Select extends Text
{
    use HasOptions;

    public function formInputHandler()
    {
        return SelectHandler::class;
    }

    public function isMultiSelect(): bool
    {
        return false;
    }

    protected function viewName(): string
    {
        return 'lego::bootstrap3.input.select';
    }
}
