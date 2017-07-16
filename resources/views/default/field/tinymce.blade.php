<?php /* @var \Lego\Field\Provider\RichText $field */ ?>

<textarea
    name="{{ $field->elementName() }}"
    id="{{ $field->elementId() }}"
    {!! \Collective\Html\HtmlFacade::attributes($field->getAttributes()) !!}
>
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
