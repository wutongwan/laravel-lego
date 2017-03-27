{!! $field !!}

@if($field->messages()->any())
    @foreach($field->messages()->all() as $message)
        <p class="text-info">{!! $message !!}</p>
    @endforeach
@endif
@if($field->errors()->any())
    @foreach($field->errors()->all() as $error)
        <p class="text-danger">
            <i class="glyphicon glyphicon-warning-sign"></i>
            {!! $error !!}
        </p>
    @endforeach
@endif
