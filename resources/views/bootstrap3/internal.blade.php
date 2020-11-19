@extends('lego::bootstrap3.layout')

@section('title', $title)

@section('content')
    {{ $set->render() }}
@endsection
