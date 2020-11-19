@extends('lego::bootstrap3.layout')

@section('title', $title ?? 'Lego Internal Page')

@section('content')
    {{ $set->render() }}
@endsection
