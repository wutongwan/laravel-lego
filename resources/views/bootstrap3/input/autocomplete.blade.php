<?php
/** @var \Lego\Input\AutoComplete $input */
$value = $input->values()->getCurrentValue();
$text = $input->values()->getExtra('text');
$textInputName = $input->getTextInputName();
?>
<select name="{{ $input->getInputName() }}"
        class="form-control lego-field-autocomplete"
        style="width: 100%; min-width: 100%;"
        data-placeholder="{{ $input->getPlaceholder() }}"
        data-min-input-length="{{ $input->getMinInputLength() }}"
        data-text-input-name="{{ $textInputName }}"
        data-url="{{ urlencode($input->getRemoteUrl()) }}"
>
    @if($value)
        <option value="{{ $value }}">{{ is_null($text) ? $value : $text }}</option>
    @endif
</select>
<input type="hidden" name="{{ $textInputName }}" value="{{ $text }}">
