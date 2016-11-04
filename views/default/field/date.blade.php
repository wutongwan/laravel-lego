<input type="text" name="{{ $field->elementName() }}" id="{{ $field->elementId() }}"
       class="form-control"
       value="{{ $field->value()->current() }}"
       placeholder="{{ $field->getPlaceholder($field->description()) }}">

<script>
    $(document).ready(function () {
        $("#{{ $field->elementId() }}").datetimepicker({
            format: "{{ $field->getJavaScriptFormat() }}",
            language: "{{ $field->getLocale() }}",
            todayBtn: "linked",
            todayHighlight: true,
            autoclose: true,
            minView: "{{ $field instanceof \Lego\Field\Provider\Datetime ? 0 : 'month'}}",
            disableTouchKeyboard: true
        })
        ;
    });
</script>