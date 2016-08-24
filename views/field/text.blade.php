<div class="form-group">
    <label for="{{ $field->elementId() }}" class="col-sm-2 control-label">{{ $field->description() }}</label>
    <div class="col-sm-10">
        <input type="email" class="form-control" id="{{ $field->elementId() }}" placeholder="{{ $field->getPlaceholder() }}">
        @if($field->messages())
            <p class="help-block"></p>
        @endif
    </div>
</div>