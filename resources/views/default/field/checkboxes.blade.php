<?php /* @var \Lego\Field\Provider\Radios $field */ ?>
<div id="{{ $field->elementId() }}" class="checkbox lego-field-checkbox">
    <ul class="list-group">

        @forelse($field->getOptions() as $value => $label)
            <li class="list-group-item">
                <input
                    type="{{ $field->getInputType() }}"
                    name="{{ $field->getInputName() }}"
                    value="{{ $value }}"
                    {{ $field->isChecked($value) ? 'checked' : null }}
                >
                <span style="margin-left: 1em;">{{ $label }}</span>
            </li>
        @empty
            <i class="text-danger">无选项</i>
        @endforelse
    </ul>
</div>
