<?
/** @var \Lego\Field\Field $field */
$__field_placeholder = $field->getPlaceholder($field->description());
?>
<input type="{{ $field->getInputType() }}" name="{{ $field->elementName() }}" id="{{ $field->elementId() }}"
       class="form-control" value="{{ $field->getDisplayValue() }}"
       placeholder="{{ $__field_placeholder }}">

@if(!(new Mobile_Detect())->isMobile())
    @push('lego-scripts')
    <script>
        $(document).ready(function () {
            $("#{{ $field->elementId() }}").datetimepicker({!! json_encode($field->getPickerOptions()) !!});
        });
    </script>
    @endpush
@endif
