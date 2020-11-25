<?php
/** @var \Lego\Input\Radios $input */
$checked = $input->getSelected();
$inputName = $input->getInputName() . ($input->isMultiSelect() ? '[]' : '');
?>
<div class="{{ $input->getInputType() }}">
    @foreach($input->getOptions() as $value => $label)
        <label style="margin-right: 1em;">
            <input type="{{ $input->getInputType() }}" name="{!! $inputName !!}"
                   value="{{ $value }}" {{ in_array($value, $checked) ? 'checked' : '' }}>
            {{ $label }}
        </label>
    @endforeach
</div>
