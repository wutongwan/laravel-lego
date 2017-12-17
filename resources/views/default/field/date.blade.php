<?php /** @var \Lego\Field\Provider\Datetime $field */ ?>
<input type="{{ $field->getInputType() }}" name="{{ $field->elementName() }}" id="{{ $field->elementId() }}"
       class="form-control" value="{{ $field->takeInputValue() }}"
       placeholder="{{ $field->getPlaceholder($field->description()) }}">

@if(!$field->nativePickerIsEnabled())
    @push('lego-scripts')
    <script>
        $(document).ready(function () {
            $("#{{ $field->elementId() }}").datetimepicker({!! json_encode($field->getPickerOptions()) !!});
        });
    </script>
    @endpush
@endif
