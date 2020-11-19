@extends('lego::bootstrap3.layout')

@section('title', $message)

@section('content')
    <div class="text-center" style="margin-top: 7%">
        <h3>{{ $message }}</h3>
    </div>
@endsection
