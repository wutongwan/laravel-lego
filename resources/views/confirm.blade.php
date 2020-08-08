@extends('lego::layout')

@section('__lego-simple-title')
    {{ $message }}
@stop

@section('__lego-simple-body')
    <style>
        #lego-confirm-message {
            font-size: 1.2em;
            margin-top: 3em;
            margin-bottom: 3em;
        }
        #lego-confirm-button {
            margin-right: 2em;
        }
    </style>

    <div id="lego-confirm" class="text-center">
        <p id="lego-confirm-message">{{ $message }}</p>
        <p class="text-center">
            <a href="{{ $confirm }}" class="btn btn-primary lego-button-prevent-repeat"
               data-lego-button-delay="{{ $delay }}"
               id="lego-confirm-button">确认</a>
            {{-- 确认按钮之所以默认 disabled 是为了防止通过禁用 JS 跳过等待时间 --}}
            <a href="{{ $cancel }}" class="btn btn-default once">取消</a>
        </p>
    </div>
@stop
