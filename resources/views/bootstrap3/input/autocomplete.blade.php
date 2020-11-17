<?php
/** @var \Lego\Input\AutoComplete $input */
$value = $input->values()->getCurrentValue();
$text = $input->getTextValue();
$textInputName = $input->getTextInputName();
?>
<select name="{{ $input->getInputName() }}"
        class="form-control lego-field-select2"
        style="width: 100%; min-width: 100%;"
        data-language="{{ app()->getLocale() }}"
        data-theme="bootstrap"
        data-width="100%"
        data-disabled="{{ $input->isDisabled() ? 'true' : 'false' }}"
        data-minimum-input-length="{{ $input->getMinInputLength() }}"
        data-placeholder="{{ $input->getPlaceholder() }}"
        data-allow-clear="{{ $input->isRequired() ? 'false' : 'true' }}"

        data-lego-url="{{ urlencode($input->getRemoteUrl()) }}"
        data-lego-text-input-name="{{ $textInputName }}"
>
    @if($value)
        <option value="{{ $value }}">{{ is_null($text) ? $value : $text }}</option>
    @endif
</select>
<input type="hidden" name="{{ $textInputName }}" value="{{ $text }}">
