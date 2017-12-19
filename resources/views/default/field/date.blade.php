<?php /** @var \Lego\Field\Provider\Datetime $field */ ?>
<input type="{{ $field->getInputType() }}" name="{{ $field->elementName() }}" id="{{ $field->elementId() }}"
       class="form-control" value="{{ $field->takeInputValue() }}"
       placeholder="{{ $field->getPlaceholder($field->description()) }}"
       style="cursor: pointer;"
>

@if(!$field->nativePickerIsEnabled())
    @push('lego-scripts')
    <script>
        $(document).ready(function () {
            $("#{{ $field->elementId() }}")
                .attr('readonly', true)
                .css('background-color', 'white')
                .datetimepicker({!! json_encode($field->getPickerOptions()) !!});
        });
    </script>
    @endpush
@endif
