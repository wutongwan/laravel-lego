<?
/** @var \Lego\Field\Provider\NumberRange $field */
$__field_value = $field->getCurrentValue();
$__field_placeholder = $field->getPlaceholder($field->description());
?>
<div class="input-group" id="{{ $field->elementId() }}">
    <input type="number" name="{{ $field->elementName() }}[min]" class="form-control"
           value="{{ $__field_value['min'] }}" placeholder="{{ $__field_placeholder }}">
    <span class="input-group-addon"> 至 </span>
    <input type="number" name="{{ $field->elementName() }}[max]" class="form-control"
           value="{{ $__field_value['max'] }}" placeholder="{{ $__field_placeholder }}">
</div>