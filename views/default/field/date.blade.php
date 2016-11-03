<input type="text" name="{{ $field->elementName() }}" id="{{ $field->elementId() }}" value="{{ $field->value()->current() }}" class="form-control">

<script>
    $(document).ready(function () {
        $("#{{ $field->elementId() }}").datepicker({
            format: "{{ $field->getJavaScriptFormat() }}",
            language: "{{ $field->getLocale() }}",
            todayBtn: "linked",
            todayHighlight: true,
            autoclose: true
        });
    });
</script>