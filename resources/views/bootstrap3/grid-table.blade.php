<?php
/** @var \Lego\Set\Grid\Grid $grid */
$paginator = $grid->getPaginator();
$hasBatch = count($grid->getBatches()) > 0
?>

@if($grid instanceof \Lego\Set\Grid\FilterGrid)
    {{ $grid->getFilter()->render() }}
    @if($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
        <h5>共找到 {{ $grid->getPaginator()->total() }} 条符合条件的记录</h5>
    @endif
@endif

<div class="lego-grid-container {{ $hasBatch ? 'lego-grid-batch' : '' }}">

    <div class="clearfix" style="margin-top: 5px; margin-bottom: 5px;">
        <div class="pull-left">
            @if($hasBatch)
                <button class="btn btn-default lego-enable-batch hide" title="多选">
                    <span class="glyphicon glyphicon-expand"></span> 多选
                </button>
                <button class="btn btn-default lego-disable-batch hide" title="关闭多选">
                    <span class="glyphicon glyphicon-collapse-down"></span> 多选
                </button>
            @endif
            @foreach($grid->buttons()->getByPosition('LeftTop') as $button)
                {{ $button->render('lego-button btn btn-default') }}
            @endforeach
        </div>
        <div class="pull-right">
            @foreach($grid->buttons()->getByPosition('RightTop') as $button)
                {{ $button->render('lego-button btn btn-default') }}
            @endforeach
        </div>
    </div>

    @if($hasBatch)
        <div class="panel panel-default lego-grid-batch-tools hide">
            <div class="panel-body">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-default lego-select-all">
                        <span class="glyphicon glyphicon-check"></span> 全选
                    </button>
                    <button class="btn btn-default lego-select-reverse">
                        <span class="glyphicon glyphicon-unchecked"></span> 反选
                    </button>
                    <button class="btn btn-default" disabled>
                        已选 <span class="lego-selected-count">0</span> 项
                    </button>
                </div>
                &middot;
                <form method="get" class="lego-batch-form" target="lego-grid-batch-frame" style="display: inline">
                    <input type="hidden" name="__lego_resp_id" value="">
                    <input type="hidden" name="__lego_ids_count" value="0">
                    <input type="hidden" name="__lego_ids" value="">
                    @foreach($grid->getBatches() as $batch)
                        <button data-name="{{ $batch->getName() }}"
                                data-resp-id="{{ rawurlencode($batch->getRespId()) }}"
                                class="btn btn-default btn-sm lego-batch-submit">
                            <span class="glyphicon glyphicon-send"></span> {{ $batch->getName() }}
                        </button>
                    @endforeach
                </form>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover table-bordered lego-grid lego-grid-table">
            <thead>
            <tr class="lego-grid-header">
                <th class="lego-batch-item hide" style="width: 1em; text-align: center;">#</th>
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
                    <td class="lego-batch-item hide">
                        <input type="checkbox" class="lego-batch-checkbox" value="{{ $row['__lego_batch_id'] }}">
                    </td>
                    @foreach($grid->getCells() as $cell)
                        <td>{{ $row[$cell->getName()->getOriginal()] }}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="text-center">{{ $paginator->links() }}</div>
</div>

@include('lego::bootstrap3.snippet.button-pair', ['set' => $grid, 'left' => 'LeftBottom', 'right' => 'RightBottom'])

<div class="modal fade" id="lego-grid-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">批处理</h4>
            </div>
            <div class="modal-body" style="padding: 0;">
                <iframe name="lego-grid-batch-frame" src="" style="height:80vh; width:100%; border: 0"></iframe>
            </div>
        </div>
    </div>
</div>
