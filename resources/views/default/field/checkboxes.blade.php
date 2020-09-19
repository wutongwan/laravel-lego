<?php /* @var \Lego\Field\Provider\Radios $field */ ?>
<div id="{{ $field->elementId() }}" class="lego-field-checkbox">
    @forelse($field->getOptions() as $value => $label)
        <div class="{{ $field->getInputType() }}">
            <label>
                <input
                    type="{{ $field->getInputType() }}"
                    name="{{ $field->getInputName() }}"
                    value="{{ $value }}"
                    {{ $field->isChecked($value) ? 'checked' : null }}
                >
                <span style="margin-left: 1em;">{{ $label }}</span>
            </label>
        </div>
    @empty
        <i class="text-danger">无选项</i>
    @endforelse
</div>
