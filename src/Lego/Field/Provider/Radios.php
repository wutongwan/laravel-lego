<?php

namespace Lego\Field\Provider;

use Lego\Field\Concerns\FilterWhereEquals;
use Lego\Field\Concerns\HasOptions;
use Lego\Foundation\Facades\LegoAssets;

class Radios extends Text
{
    use FilterWhereEquals;
    use HasOptions;

    protected $inputType = 'radio';

    protected function renderEditable()
    {
        LegoAssets::js('components/icheck/icheck.min.js');
        LegoAssets::css('components/icheck/skins/square/blue.css');

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
