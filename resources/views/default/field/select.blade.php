<select name="{{ $field->elementName() }}" id="{{ $field->elementId() }}" class="form-control"
        {{ \Collective\Html\HtmlFacade::attributes($attributes ?? []) }}>
    @if(!is_null($placeholder = $field->getPlaceholder()))
        <option value="">* {{ $placeholder }} *</option>
    @endif
    <? /** @var \Lego\Field\Provider\Select $field */ ?>
    <? $__field_value = $field->getCurrentValue(); ?>
    @foreach($field->getOptions() as $value => $option)
        <option value="{{ $value }}" {{ $value == $__field_value ? 'selected' : '' }}>{{ $option }}</option>
    @endforeach
</select>
