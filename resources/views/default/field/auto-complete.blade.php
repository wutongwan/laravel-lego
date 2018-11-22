<?php /** @var \Lego\Field\Provider\AutoComplete $field */ ?>

<select name="{{ $field->elementName() }}" id="{{ $field->elementId() }}" class="form-control"
        style="width: 100%; min-width: 100%;">
    @if($value = $field->takeInputValue())
        <option value="{{ $value }}">{{ $field->takeShowValue() }}</option>
    @endif
</select>
<input type="hidden"
       id="{{ $field->elementId() }}{{ $field->getLabelElementSuffix() }}"
       name="{{ $field->elementName() }}{{ $field->getLabelElementSuffix() }}"
       value="{{ $field->takeShowValue() }}"
>

@push('lego-scripts')
<script>
    $(document).ready(function () {
        var $select = $("#{{ $field->elementId() }}");

        $select.select2({
            placeholder: "{{ $field->getPlaceholder() }}",
            theme: "bootstrap",
            width: "100%",
            language: "{{ $field->getLocale() }}",
            allowClear: eval("{{ $field->isRequired() ? 'false' : 'true' }}"),
            minimumInputLength: eval("{{ $field->getMin() }}"),
            ajax: {
                url: "{!! $field->remote() !!}",
                dataType: 'json',
                delay: 700,
                cache: true,
                data: function (params) {
                    return {
                        "{{ \Lego\Register\AutoCompleteMatchHandler::KEYWORD_KEY }}": params.term,
                        "page": params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.value,
                                text: item.label,
                            }
                        }),
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            } // let our custom formatter work
        });

        // fill text input
        var $selectedText = $('#{{ $field->elementId() }}-text');

        $select.on('select2:select', function (event) {
            $selectedText.val(event.params.data.text);
        });

        $select.on('select2:unselect', function () {
            $selectedText.val(null);
        });
    });
</script>
@endpush
