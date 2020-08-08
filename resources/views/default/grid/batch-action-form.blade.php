@extends('lego::default.layout')

@section('__lego-simple-title')
    {{ $action->name() }}
@endsection

@section('__lego-simple-body')
    <link href="//cdn.bootcdn.net/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
'
    {!! $form->render() !!}
@endsection
