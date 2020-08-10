@extends('lego::default.layout')

@section('__lego-simple-title')
    {{ $action->name() }}
@endsection

@section('__lego-simple-body')
    {!! $form->render() !!}
@endsection
