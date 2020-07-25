<?php /** @var \Lego\Field\Provider\AutoComplete $field */ ?>

<select name="{{ $field->elementName() }}"
        id="{{ $field->elementId() }}"
        class="form-control"
        style="width: 100%; min-width: 100%;"
        data-placeholder="{{ $field->getPlaceholder() }}"
        data-language="{{ $field->getLocale() }}"
        data-allow-clear="{{ $field->isRequired() ? 'false' : 'true' }}"
        data-min-input-length="{{ $field->getMin() }}"
        data-url="{{ urlencode($field->remote()) }}"
        data-text-input-id="{{ $field->elementId() }}{{ $field->getLabelElementSuffix() }}"
>
    @if($value = $field->takeInputValue())
        <option value="{{ $value }}">{{ $field->takeShowValue() }}</option>
    @endif
</select>
<input type="hidden"
       id="{{ $field->elementId() }}{{ $field->getLabelElementSuffix() }}"
       name="{{ $field->elementName() }}{{ $field->getLabelElementSuffix() }}"
       value="{{ $field->takeShowValue() }}"
>
