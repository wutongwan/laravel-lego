<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;

class Textarea extends Text
{
    protected function renderEditable()
    {
        return FormFacade::textarea(
            $this->elementName(),
            $this->getDisplayValue(),
            ['id' => $this->elementId(), 'class' => 'form-control']
        );
    }
}
