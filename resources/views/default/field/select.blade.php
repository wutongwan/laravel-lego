<? /** @var \Lego\Field\Provider\Select $field */ ?>
<? $__field_value = $field->getCurrentValue(); ?>
<select name="{{ $field->elementName() }}" id="{{ $field->elementId() }}" class="form-control">
    @if($placeholder = $field->getPlaceholder())
        <option value="">** {{ $placeholder }} **</option>
    @endif
    @foreach($field->getOptions() as $value => $option)
        <option value="{{ $value }}" {{ $value == $__field_value ? 'selected' : '' }}>{{ $option }}</option>
    @endforeach
</select>