@extends('lego::default.grid.layout')

<?php
/** @var \Lego\Widget\Grid\Grid $grid */
/** @var \Lego\Operator\Store $row */
?>

@section('grid-body')
    <?php $batchModeEnabled = $grid->batchModeEnabled(); ?>

    <div class="table-responsive">
        <table class="table lego-grid lego-grid-table" id="{{ $grid->uniqueId() }}">
            <tr>
                @if($batchModeEnabled)
                    <th>#</th>
                @endif
                @foreach($grid->cells() as $cell)
                    <th>{{ $cell->description() }}</th>
                @endforeach
            </tr>
            @foreach($grid->paginator() as $row)
                <tr>
                    @if($batchModeEnabled)
                        <td><input type="checkbox" class="lego-batch-checkbox"
                                   value="{{ $row->get($grid->getBatchIdName()) }}"></td>
                    @endif
                    @foreach($grid->cells() as $cell)
                        <td>{{ $cell->fill($row)->value() }}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
@stop
