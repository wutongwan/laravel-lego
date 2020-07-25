<?php

namespace Lego\Field\Provider;

use Lego\Field\Concerns\HasOptions;
use Lego\Foundation\Facades\LegoAssets;

class Radios extends Text
{
    use HasOptions;

    protected $inputType = 'radio';
    protected $queryOperator = self::QUERY_EQ;

    protected function renderEditable()
    {
        return $this->view('lego::default.field.checkboxes');
    }

    protected function renderReadonly()
    {
        $key = $this->takeShowValue();

        return isset($this->options[$key]) ? $this->options[$key] : $key;
    }

    public function isChecked($value)
    {
        return $this->takeInputValue() == $value;
    }

    public function getInputName()
    {
        return $this->elementName();
    }
}
