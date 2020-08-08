@extends('lego::default.grid.layout')

<?php
/** @var \Lego\Widget\Grid\Grid $grid */
/** @var \Lego\Operator\Store $row */
?>

@section('grid-body')

    <div id="{{ $grid->uniqueId() }}">
        <ul class="list-group lego-grid lego-grid-list-group">
            @foreach($grid->paginator() as $row)
                <li class="list-group-item" data-lego-batch-id="{{ $row->get($grid->getBatchIdName()) }}">
                    @if($grid->batchModeEnabled())
                        <span class="pull-right">
                            <input type="checkbox" class="lego-batch-checkbox"
                                   value="{{ $row->get($grid->getBatchIdName()) }}">
                        </span>
                    @endif
                    @foreach($grid->cells() as $cell)
                        <p>
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
