<?php

namespace Lego\Field\Provider;

/**
 * Select2 - The jQuery replacement for select boxes.
 */
class Select2 extends Select
{
    public function renderEditable()
    {
        $this->setAttribute([
            'class' => 'lego-field-select2',
            'data-placeholder' => $this->getPlaceholder(),
            'data-language' => $this->getLocale(),
            'data-allow-clear' => $this->isRequired() ? 'false' : 'true',
        ]);

        return parent::renderEditable();
    }
}
