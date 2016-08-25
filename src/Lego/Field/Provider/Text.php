<?php namespace Lego\Field\Provider;

use Lego\Field\Field;
use Lego\Helper\HtmlUtility;

class Text extends Field
{
    public function render() : string
    {
        // TODO mode
        return HtmlUtility::form()->input(
            'text',
            $this->elementName(),
            $this->getOriginalValue(),
            $this->getAttributes()
        );
    }
}