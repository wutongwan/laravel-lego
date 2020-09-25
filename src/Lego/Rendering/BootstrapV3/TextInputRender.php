<?php

namespace Lego\Rendering\BootstrapV3;

use Lego\Input\Text;
use Lego\Utility\HtmlUtility;

class TextInputRender
{
    public function render(Text $input)
    {
        return HtmlUtility::input('text', $input->getInputName(), $input->getValue(), ['class' => 'form-control']);
    }
}
