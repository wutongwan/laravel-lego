<?php

declare(strict_types=1);

namespace Lego\Input\Form;

class NumberHandler extends TextHandler
{
    public function beforeRender(): void
    {
        parent::beforeRender();

        $attributes = $this->input->attributes();
        if ($attributes->has('min')) {
            $this->wrapper->rule("min:" . $attributes->get('min'));
        }
        if ($attributes->has('max')) {
            $this->wrapper->rule("max:" . $attributes->get('max'));
        }
    }
}
