<?
/** @var \Lego\Field\Field $field */
$__field_placeholder = $field->getPlaceholder($field->description());
?>
<input type="{{ $field->getInputType() }}" name="{{ $field->elementName() }}" id="{{ $field->elementId() }}"
       class="form-control" value="{{ $field->value()->show() }}"
       placeholder="{{ $__field_placeholder }}">

@if(!(new Mobile_Detect())->isMobile())
    <script>
        $(document).ready(function () {
            $("#{{ $field->elementId() }}").datetimepicker({!! json_encode($field->getPickerOptions()) !!});
        });
    </script>
@endif
