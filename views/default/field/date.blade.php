<?
/** @var \Lego\Field\Field $field */
$__field_value = $field->value()->current();
?>
<input type="{{ $field->getInputType() }}" name="{{ $field->elementName() }}" id="{{ $field->elementId() }}"
       class="form-control" value="{{ $__field_value ? $__field_value->format($field->getFormat()) : null }}"
       placeholder="{{ $field->getPlaceholder($field->description()) }}">

@if(!(new Mobile_Detect())->isMobile())
    <script>
        $(document).ready(function () {
            $("#{{ $field->elementId() }}")
                .datetimepicker({!! json_encode($field->getPickerOptions()) !!});
        });
    </script>
@endif
