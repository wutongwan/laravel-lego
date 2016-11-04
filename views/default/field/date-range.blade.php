<?
/** @var \Lego\Field\Field $field */
$__field_value = $field->value()->current();
$__field_placeholder = $field->getPlaceholder($field->description());
?>
<div class="input-group" id="{{ $field->elementId() }}">
    <span class="input-group-addon"> >= </span>
    <input type="text" name="{{ $field->elementName() }}[min]" class="form-control"
           value="{{ $__field_value['min'] ?? '' }}" placeholder="{{ $__field_placeholder }}">
    <span class="input-group-addon"> <= </span>
    <input type="text" name="{{ $field->elementName() }}[max]" class="form-control"
           value="{{ $__field_value['max'] ?? '' }}" placeholder="{{ $__field_placeholder }}">
</div>

<script>
    $(document).ready(function () {
        $("#{{ $field->elementId() }} input").each(function () {
            $(this).datetimepicker({
                format: "{{ $field->getJavaScriptFormat() }}",
                language: "{{ $field->getLocale() }}",
                todayBtn: "linked",
                todayHighlight: true,
                autoclose: true,
                startView: "{{ $field->getStartView() }}",
                minView: "{{ $field->getMinView() }}",
                maxView: "{{ $field->getMaxView() }}",
                disableTouchKeyboard: true
            });
        });
    });
</script>