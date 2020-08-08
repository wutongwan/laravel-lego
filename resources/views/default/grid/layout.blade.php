<?php
/** @var \Lego\Widget\Grid\Grid $grid */
$paginator = $grid->paginator();
$hasBatch = $grid->batchModeEnabled();
?>

@if($grid->filter())
@section('grid-filter')
    {!! $grid->filter() !!}
    <h5>共找到 {{ $grid->paginator()->total() }} 条符合条件的记录</h5>
    <hr>
@show
@endif

@include('lego::default.snippets.top-buttons', ['widget' => $grid])

<div id="{{ $grid->uniqueId() }}" class="{{ $hasBatch ? 'lego-grid-batch' : '' }}">
    @if($hasBatch)
        <div class="panel panel-default lego-grid-batch-tools">
            <div class="panel-body">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-default lego-select-all" v-on:click="selectAll">
                        <span class="glyphicon glyphicon-check"></span> 全选
                    </button>
                    <button class="btn btn-default lego-select-reverse" v-on:click="selectReverse">
                        <span class="glyphicon glyphicon-unchecked"></span> 反选
                    </button>
                    <button class="btn btn-default">
                        已选 <span class="lego-selected-count">0</span> 项
                    </button>
                </div>
                &middot;
                <form method="post" class="lego-batch-form">
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
        <div class="text-center">
            {!! $paginator->links() !!}
        </div>
    @show
</div>

@include('lego::default.snippets.bottom-buttons', ['widget' => $grid])
