<?php
/** @var \Lego\Field\Field $field */
if ($field->errors()->any()) {
    $field->container('class', 'has-error');
}
?>
<div {!! \Collective\Html\HtmlFacade::attributes($field->containerAttributes()) !!}>
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
