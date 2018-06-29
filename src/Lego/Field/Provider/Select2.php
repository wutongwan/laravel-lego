<?php

namespace Lego\Field\Provider;

use Lego\Field\Concerns\HasSelect2Assets;

/**
 * Select2 - The jQuery replacement for select boxes.
 */
class Select2 extends Select
{
    use HasSelect2Assets;

    public function renderEditable()
    {
        $this->includeSelect2Assets();

        return $this->view('lego::default.field.select2')
            ->with('select', parent::renderEditable());
    }
}
