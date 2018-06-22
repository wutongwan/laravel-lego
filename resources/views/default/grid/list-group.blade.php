@extends('lego::default.grid.layout')

<?php
/** @var \Lego\Widget\Grid\Grid $grid */
/** @var \Lego\Operator\Store $row */
?>

@section('grid-body')

    <div id="{{ $grid->uniqueId() }}">
        <ul class="list-group">
            @foreach($grid->paginator() as $row)
                <li class="list-group-item"
                    @if($grid->batchModeEnabled())
                    v-on:click="trigger({{ $row->get($grid->getBatchIdName()) }})"
                    @endif
                >
                    @if($grid->batchModeEnabled())
                        <span class="pull-right">
                            <input type="checkbox" v-model="selectedIds" value="{{ $row->get($grid->getBatchIdName()) }}">
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
