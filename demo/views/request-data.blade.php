@php($title = 'Request Data')

@extends('lego-demo::layout')

@section('content')
    <h2>表单数据</h2>
    <div class="col-md-8">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th style="width: 50%">Input Name</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            @foreach(request()->all() as $name => $value)
                <tr>
                    <td>{{ $name }}</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
