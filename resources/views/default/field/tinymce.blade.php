<?php /* @var \Lego\Field\Provider\RichText $field */ ?>

<textarea {{ $field->getAttributesString() }}>
    {!! $field->takeInputValue() !!}
</textarea>

@push('lego-scripts')
<script>
    $(document).ready(function () {
        tinymce.init({
            selector: '#{{ $field->elementId() }}',
            menubar: false
        });
    });
</script>
@endpush
