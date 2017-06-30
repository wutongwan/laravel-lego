<?php /* @var \Lego\Field\Provider\CheckboxGroup $field */ ?>
<div id="{{ $field->elementId() }}" class="checkbox">
    <ul class="list-group">
        @forelse($field->getOptions() as $value => $label)
            <li class="list-group-item">
                <input type="checkbox"
                       name="{{ $field->elementName() }}[]"
                       value="{{ $value }}"
                    {{ in_array($value, $field->takeInputValue()) ? 'checked' : null }}
                >
                <span style="margin-left: 1em;">{{ $label }}</span>
            </li>
        @empty
            <i class="text-danger">无选项</i>
        @endforelse
    </ul>
</div>

@push('lego-scripts')
<script>
    (function () {
        $('#{{ $field->elementId() }} input').each(function (idx, box) {
            var $box = $(box);
            $box.iCheck({checkboxClass: 'icheckbox_square-blue'});
            $box.closest('.list-group-item').on('click', function () {
                $box.iCheck('toggle')
            });
        });
    })();
</script>
@endpush
