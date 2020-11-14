<?php

namespace Lego\Rendering\Bootstrap3\Input;

use Lego\Input\Hidden;

class HiddenInputRender
{
    public function handle(Hidden $input)
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            $input->getInputName(),
            $input->getCurrentValue()
        );
    }
}
