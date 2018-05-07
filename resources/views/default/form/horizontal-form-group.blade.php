<?php
/** @var \Lego\Field\Field $field */
if ($field->errors()->any()) {
    $field->getContainer()->setAttribute('class', 'has-error');
}
?>
<div {!! $field->getContainer()->geAttributesString() !!}>
    <label for="{{ $field->elementId() }}" class="col-sm-2 control-label">
        @if($field->isRequired() && $field->isEditable())
            <span class="text-danger">*</span>
        @endif
        {{ $field->description() }}
    </label>
    <div class="col-sm-10">
        @include('lego::default.form.field', ['field' => $field])
    </div>
</div>
