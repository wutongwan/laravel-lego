<?php

namespace Lego\Rendering\Bootstrap3;

use Lego\Input\Text;
use Lego\Utility\HtmlUtility;

class TextInputRender
{
    public function render(Text $input)
    {
        return HtmlUtility::input(
            'text',
            $input->getInputName(),
            $input->values()->getCurrentValue(),
            ['class' => 'form-control']
        );
    }
}
