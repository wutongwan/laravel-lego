<?
/** @var \Lego\Field\Field $field */
$__field_value = $field->value()->current();
?>
<input type="text" name="{{ $field->elementName() }}" id="{{ $field->elementId() }}"
       class="form-control"
       value="{{ $__field_value ? $__field_value->format($field->getFormat()) : null }}"
       placeholder="{{ $field->getPlaceholder($field->description()) }}">

<script>
    $(document).ready(function () {
        $("#{{ $field->elementId() }}").datetimepicker({
            format: "{{ $field->getJavaScriptFormat() }}",
            language: "{{ $field->getLocale() }}",
            todayBtn: "linked",
            todayHighlight: true,
            autoclose: true,
            startView: "{{ $field->getStartView() }}",
            minView: "{{ $field->getMinView() }}",
            maxView: "{{ $field->getMaxView() }}",
            disableTouchKeyboard: true
        })
        ;
    });
</script>