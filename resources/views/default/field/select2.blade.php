<?php /* @var \Lego\Field\Provider\Select2 $field */ ?>

{!! $select !!}

@push('lego-scripts')
<script>
    $(document).ready(function () {
        $("#{{ $field->elementId() }}").select2({
            placeholder: "{{ $field->getPlaceholder() }}",
            theme: "bootstrap",
            width: "100%",
            language: "{{ $field->getLocale() }}",
            allowClear: eval("{{ $field->isRequired() ? 'false' : 'true' }}")
        });
    });
</script>
@endpush

