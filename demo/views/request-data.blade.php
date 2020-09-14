@php($title = 'Request Data')

@extends('lego-demo::layout')

@section('content')
    <ul class="list-group">
        @foreach(\Illuminate\Support\Facades\Request::all() as $key => $value)
            <li class="list-group-item">
                <p><strong>{{ $key }}</strong></p>
                <p>
                    <?php dump($value); ?>
                </p>
            </li>
        @endforeach
    </ul>
@endsection
