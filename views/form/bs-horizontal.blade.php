<form method="post" class="form-horizontal">
    @foreach($form->fields() as $field)
        <div class="form-group">
            <label for="{{ $field->elementId() }}" class="col-sm-2 control-label">{{ $field->description() }}</label>
            <div class="col-sm-10">
                {!! $field->render() !!}
                @if($field->messages()->any())
                    @foreach($field->messages() as $message)
                        <p class="help-block"><i class="icon-info-sign"></i> {!! $message !!}</p>
                    @endforeach
                @endif
                @if($field->errors()->any())
                    @foreach($field->errors() as $error)
                        <p class="help-block text-danger"><i class="icon-remove-sign"></i> {!! $error !!}</p>
                    @endforeach
                @endif
            </div>
        </div>
    @endforeach

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </div>
</form>