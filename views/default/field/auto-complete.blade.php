<select name="{{ $field->elementName() }}" id="{{ $field->elementId() }}" class="form-control"
        style="width: 100%; min-width: 100%;">
    @if($value = $field->value()->current())
        <option value="{{ $value }}">{{ $field->value()->show() }}</option>
    @endif
</select>

<script>
    $(document).ready(function () {
        $("#{{ $field->elementId() }}").select2({
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
                        "{{ \Lego\Register\Data\AutoCompleteData::KEYWORD_KEY }}": params.term,
                        "page": params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.items,
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
    });
</script>