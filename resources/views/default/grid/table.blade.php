@extends('lego::default.grid.layout')

@section('grid-body')
    <?php /** @var \Lego\Widget\Grid\Grid $grid */ ?>
    <?php $batchModeEnabled = $grid->batchModeEnabled(); ?>

    <div class="table-responsive">
        <table class="table" id="{{ $grid->uniqueId() }}">
            <tr>
                @if($batchModeEnabled)
                    <th>#</th>
                @endif
                @foreach($grid->cells() as $cell)
                    <th>{{ $cell->description() }}</th>
                @endforeach
            </tr>
            <?php /** @var \Lego\Operator\Store $row */ ?>
            @foreach($grid->paginator() as $row)
                <tr>
                    @if($batchModeEnabled)
                        <td>
                            @include('lego::default.grid.batch-checkbox', ['batchId' => $grid->getBatchIdName()])
                        </td>
                    @endif
                    @foreach($grid->cells() as $cell)
                        <td v-pre>{{ $cell->fill($row)->value() }}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
@stop
