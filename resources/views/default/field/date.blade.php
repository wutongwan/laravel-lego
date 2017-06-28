<?php /** @var \Lego\Field\Field $field */ ?>
<input type="{{ $field->getInputType() }}" name="{{ $field->elementName() }}" id="{{ $field->elementId() }}"
       class="form-control" value="{{ $field->takeDefaultInputValue() }}"
       placeholder="{{ $field->getPlaceholder($field->description()) }}">

@if(!app(\Mobile_Detect::class)->isMobile())
    @push('lego-scripts')
    <script>
        $(document).ready(function () {
            $("#{{ $field->elementId() }}").datetimepicker({!! json_encode($field->getPickerOptions()) !!});
        });
    </script>
    @endpush
@endif
