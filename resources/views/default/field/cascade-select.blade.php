<?php /* @var \Lego\Field\Provider\CascadeSelect $field */ ?>

<select name="{{ $field->elementName() }}"
        id="{{ $field->elementId() }}"
        v-model="selected"
        data-lego-cascade-select
        data-seleted="{{ $field->takeInputValue() }}"
        data-depend="{{ $field->getDependField()->elementId() }}"
        data-remote="{{ rawurlencode($field->getRemote()) }}"
    {!! $field->getAttributesString() !!}>
    @foreach($field->getOptions() as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>
