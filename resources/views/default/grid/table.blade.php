@extends('lego::default.grid.layout')

@section('grid-body')
    <div class="table-responsive">
        <table class="table" id="{{ $grid->uniqueId() }}">
            <tr>
                @if($grid->batchModeEnabled())
                    <th>#</th>
                @endif
                @foreach($grid->cells() as $cell)
                    <th>{{ $cell->description() }}</th>
                @endforeach
            </tr>
            <?php /** @var \Lego\Operator\Store\Store $row */ ?>
            @foreach($grid->paginator() as $row)
                <tr>
                    @if($grid->batchModeEnabled())
                        <td>
                            @include('lego::default.grid.batch-checkbox', ['batchId' => $row->getKey()])
                        </td>
                    @endif
                    @foreach($grid->cells() as $cell)
                        <td>{{ $cell->fill($row)->value() }}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
@stop
