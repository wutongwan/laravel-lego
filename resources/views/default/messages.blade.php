{{-- 信息展示控件

Params: $object, object with trait \Lego\Helper\MessageOperator
--}}

@if($object->errors()->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($object->errors()->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($object->messages()->any())
    <div class="alert alert-info">
        <ul>
            @foreach($object->messages()->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif