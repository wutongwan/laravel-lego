@extends('lego::grid.action.layout')

@section('title')
    {{ $message }}
@endsection

@push('lego-styles')
<style>
    h2 {
        margin-bottom: 2em;
    }
</style>
@endpush

<?php $level = isset($level) ? $level : 'info' ?>

@section('content')
    <div class="text-center">
        <h2 class="text-{{ $level }}">
            <span class="glyphicon glyphicon-{{ $level }}-sign"></span>
            {{ $message }}
        </h2>
        <p>
            <a href='javascript:history.go(-1);' class="btn btn-default">
                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                返回上一页面
            </a>
        </p>
    </div>
@endsection
