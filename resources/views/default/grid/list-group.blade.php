@extends('lego::default.grid.layout')

@section('grid-body')
    <div id="{{ $grid->uniqueId() }}">
        <ul class="list-group">
            @foreach($grid->paginator() as $row)
                <li class="list-group-item"
                    @if($grid->batchModeEnabled())
                    v-on:click="trigger({{ $row->getKey() }})"
                    @endif
                >
                    @if($grid->batchModeEnabled())
                        <span class="pull-right">
                        @include('lego::default.grid.batch-checkbox', ['batchId' => $row->getKey()])
                        </span>
                    @endif

                    @foreach($grid->cells() as $cell)
                        <p v-pre>
                            @if($description = $cell->description())
                                <small>{{ $description }}：</small>
                            @endif
                            {{ $cell->copy()->fill($row)->value() }}
                        </p>
                    @endforeach
                </li>
            @endforeach
        </ul>
    </div>
@stop
