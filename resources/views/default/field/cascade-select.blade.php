<?php /* @var \Lego\Field\Provider\CascadeSelect $field */ ?>

<select name="{{ $field->elementName() }}"
        id="{{ $field->elementId() }}"
        data-lego-cascade-select
        data-selected="{{ $field->takeInputValue() }}"
        data-depend="{{ $field->getDependField()->elementId() }}"
        data-remote="{{ rawurlencode($field->getRemote()) }}"
        data-required="{{ $field->isRequired() ? 'true' : 'false' }}"
        data-placeholder="{{ $field->getPlaceholder() ?: $field->name() }}"
    {!! $field->getAttributesString() !!}>
    @foreach($field->getOptions() as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>
