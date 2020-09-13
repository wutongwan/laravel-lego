<?php
/** @var \Lego\Widget\Grid\Grid $grid */
$paginator = $grid->paginator();
$hasBatch = count($grid->batches()) > 0
?>

@if($grid->filter())
@section('grid-filter')
    {!! $grid->filter() !!}
    <h5>共找到 {{ $grid->paginator()->total() }} 条符合条件的记录</h5>
    <hr>
@show
@endif

<div id="{{ $grid->uniqueId() }}" class="lego-grid-container{{ $hasBatch ? ' lego-grid-batch' : '' }}">
    <div class="clearfix" style="margin-bottom: 5px;">
        <div class="lego-left-top-buttons pull-left">
            @if($hasBatch)
                <button class="btn btn-default lego-enable-batch hide" title="多选">
                    <span class="glyphicon glyphicon-expand"></span> 多选
                </button>
                <button class="btn btn-default lego-disable-batch hide" title="关闭多选">
                    <span class="glyphicon glyphicon-collapse-down"></span> 多选
                </button>
            @endif
            @foreach($grid->getButtons('left-top') as $button)
                {!! $button !!}
            @endforeach
        </div>
        <div class="lego-right-top-buttons pull-right">
            @foreach($grid->getButtons('right-top') as $button)
                {!! $button !!}
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
                <form method="post" class="lego-batch-form" style="display: inline">
                    <input type="hidden" name="ids" value="">
                    {{ csrf_field() }}
                    @foreach($grid->batches() as $batch)
                        <button data-action="{{ rawurlencode($batch->url()) }}"
                                data-open-target="{{ $batch->getOpenTarget() }}"
                                data-name="{{ $batch->name() }}"
                                class="btn btn-default btn-sm lego-batch-submit">
                            <span class="glyphicon glyphicon-send"></span> {{ $batch->name() }}
                        </button>
                    @endforeach
                </form>
            </div>
        </div>
    @endif

    @yield('grid-body')

    @section('grid-paginator')
        <div class="text-center">{!! $paginator->links() !!}</div>
    @show
</div>

@include('lego::default.snippets.bottom-buttons', ['widget' => $grid])
