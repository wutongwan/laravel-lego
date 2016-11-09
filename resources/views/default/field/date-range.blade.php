<?
/** @var \Lego\Field\Provider\Datetime $field */
$__field_value = $field->value()->show();
$__field_placeholder = $field->getPlaceholder($field->description());
?>
<div class="input-group" id="{{ $field->elementId() }}">
    <input type="{{ $field->getInputType() }}" name="{{ $field->elementName() }}[min]" class="form-control"
           value="{{ $__field_value['min'] ?? '' }}" placeholder="{{ $__field_placeholder }}">
    <span class="input-group-addon"> 至 </span>
    <input type="{{ $field->getInputType() }}" name="{{ $field->elementName() }}[max]" class="form-control"
           value="{{ $__field_value['max'] ?? '' }}" placeholder="{{ $__field_placeholder }}">
</div>

@if(!(new Mobile_Detect())->isMobile())
    <script>
        $(document).ready(function () {
            $("#{{ $field->elementId() }} input").each(function () {
                $(this).datetimepicker({!! json_encode($field->getPickerOptions()) !!});
            });
        });
    </script>
@endif