<?php

namespace Lego\Field\Provider;

use Illuminate\Support\HtmlString;
use Lego\Utility\HtmlUtility;

class RichText extends Textarea
{
    protected function renderReadonly()
    {
        return new HtmlString($this->takeShowValue());
    }

    protected function renderEditable()
    {
        $this->setAttribute(['class' => 'lego-field-tinymce']);
        return HtmlUtility::tag('textarea', $this->takeInputValue(), $this->getAttributes());
    }
}
