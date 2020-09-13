@extends('lego::default.grid.layout')

<?php
/** @var \Lego\Widget\Grid\Grid $grid */
/** @var \Lego\Operator\Store $row */
?>

@section('grid-body')
    <div class="table-responsive">
        <table class="table lego-grid lego-grid-table" id="{{ $grid->uniqueId() }}">
            <tr>
                <th class="lego-batch-item hide">#</th>
                @foreach($grid->cells() as $cell)
                    <th>{{ $cell->description() }}</th>
                @endforeach
            </tr>
            @foreach($grid->paginator() as $row)
                <tr>
                    <td class="lego-batch-item hide">
                        <input type="checkbox" class="lego-batch-checkbox"
                               value="{{ $row->get($grid->getBatchIdName()) }}">
                    </td>
                    @foreach($grid->cells() as $cell)
                        <td>{{ $cell->fill($row)->value() }}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
@stop
