@extends('lego::layout')

@section('title')
    {{ $action->name() }}
@endsection

@section('content')
    {!! $form !!}
@endsection
