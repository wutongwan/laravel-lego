@extends('lego-demo::layout')

@section('content')
    <style>
        .col-md-6 {
            padding-left: 2px;
            padding-right: 2px;
        }
    </style>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading text-center">{{ $title }} &middot; Code</div>
            <div class="panel-body">
                <pre><code class="php">{!! $code !!}</code></pre>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading text-center">{{ $title }} &middot; Demo</div>
            <div class="panel-body">
                @if(method_exists($widget, 'render'))
                    {{ $widget->render() }}
                @else
                    {{ $widget }}
                @endif
            </div>
        </div>
    </div>
@endsection
