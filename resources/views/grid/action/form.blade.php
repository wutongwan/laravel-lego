@extends('lego::grid.action.layout')

@section('title')
    {{ $action->name() }}
@endsection

@section('content')
    {!! $form !!}
@endsection
