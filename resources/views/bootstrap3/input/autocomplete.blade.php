<?php
/** @var \Lego\Input\AutoComplete $input */
$values = $input->getCurrentValueArray()
?>
<select name="{{ $input->getInputName() }}[value]"
        class="form-control lego-field-autocomplete"
        style="width: 100%; min-width: 100%;"
        data-placeholder="{{ $input->getPlaceholder() }}"
        data-min-input-length="{{ $input->getMinInputLength() }}"
        data-url="{{ urlencode($input->getRemoteUrl()) }}"
>
    @if($values)
        <option value="{{ $values['value'] }}">{{ $values['label'] }}</option>
    @endif
</select>
<input type="hidden" name="{{ $input->getInputName() }}[label]" value="{{ $values['label'] ?? '' }}">
