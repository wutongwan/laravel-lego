<select name="{{ $field->elementName() }}" id="{{ $field->elementId() }}" class="form-control">
    @if($value = $field->value()->current())
        <option value="{{ $value }}">{{ $value }}</option>
    @endif
</select>

<script>
    $(document).ready(function () {
        $("#{{ $field->elementId() }}").select2({
            placeholder: "{{ $field->getPlaceholder() }}",
            theme: "bootstrap",
            language: "{{ \App::getLocale() }}",
            ajax: {
                url: "{{ $field->remote() }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        "{{ \Lego\Field\Provider\AutoComplete::KEYWORD_KEY }}": params.term,
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
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1
        });
    });
</script>