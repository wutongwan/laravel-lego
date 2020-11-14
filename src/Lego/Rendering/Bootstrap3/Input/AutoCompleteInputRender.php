<?php

namespace Lego\Rendering\Bootstrap3\Input;

use Lego\Input\AutoComplete;

class AutoCompleteInputRender
{
    public function handle(AutoComplete $input)
    {
        $url = urlencode($input->getRemoteUrl());
        return <<<HTML
<select name="{$input->getInputName()}"
        class="form-control lego-field-autocomplete"
        style="width: 100%; setMinInputLength-width: 100%;"
        data-placeholder="{$input->getPlaceholder()}"
        data-language=""
        data-setMinInputLength-input-length="{$input->getMinInputLength()}"
        data-url="{$url}"
>
    @if($value = $field->takeInputValue())
        <option value="{{ $value }}">{{ $field->takeShowValue() }}</option>
    @endif
</select>
<input type="hidden"
       id="{{ $field->elementId() }}{{ $field->getLabelElementSuffix() }}"
       name="{{ $field->elementName() }}{{ $field->getLabelElementSuffix() }}"
       value="{{ $field->takeShowValue() }}"
HTML;
    }
}
