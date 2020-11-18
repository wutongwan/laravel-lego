<?php
/** @var \Lego\Set\Grid\Grid $grid */
$paginator = $grid->getPaginator();
?>

@include('lego::bootstrap3.snippet.button-pair', ['set' => $grid, 'left' => 'LeftTop', 'right' => 'RightTop'])

@if($grid instanceof \Lego\Set\Grid\FilterGrid)
    {{ $grid->getFilter()->render() }}
    @if($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
        <h5>共找到 {{ $grid->getPaginator()->total() }} 条符合条件的记录</h5>
    @endif
@endif

<div class="lego-grid-container">

    <div class="table-responsive">
        <table class="table table-hover table-bordered lego-grid lego-grid-table">
            <thead>
            <tr class="lego-grid-header">
                @foreach($grid->getCells() as $cell)
                    <th>{{ $cell->getDescription() }}
                        @if($cell->isSortAble())
                            <span class="pull-right">
                                <a href="#"
                                   data-sort="{{ $cell->getName()->getColumn() }}"
                                   data-sort-direction="asc">
                                    <span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span>
                                </a>
                                <a href="#"
                                   data-sort="{{ $cell->getName()->getColumn() }}"
                                   data-sort-direction="desc">
                                    <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
                                </a>
                            </span>
                        @endif
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($grid->getRows() as $row)
                <tr>
                    @foreach($row as $item)
                        <td>{{ $item }}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="text-center">{{ $paginator->links() }}</div>
</div>

@include('lego::bootstrap3.snippet.button-pair', ['set' => $grid, 'left' => 'LeftBottom', 'right' => 'RightBottom'])
