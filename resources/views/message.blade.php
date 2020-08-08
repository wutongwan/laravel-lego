@extends('lego::layout')

@section('__lego-simple-title')
    {{ $message }}
@endsection

<?php $level = isset($level) ? $level : 'info' ?>

@section('__lego-simple-body')
    <style>
        h2 {
            margin-bottom: 2em;
        }

        #lego-message {
            font-size: 2.2em;
            margin-top: 1.5em;
            margin-bottom: 1.5em;
        }
    </style>

    <div class="text-center">
        <p id="lego-message" class="text-{{ $level }}">
            {{ $message }}
        </p>
        <p>
            <a href='javascript:history.go(-1);' class="btn btn-default">
                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                返回上一页面
            </a>
        </p>
    </div>
@endsection
