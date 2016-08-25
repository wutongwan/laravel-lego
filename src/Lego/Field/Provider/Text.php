<?php namespace Lego\Field\Provider;

use Lego\Field\Field;

class Text extends Field
{
    public function render() : string
    {
        // TODO mode
        return lego_form_builder()->input(
            'text',
            $this->elementName(),
            $this->getOriginalValue(),
            $this->getAttributes()
        );
    }
}