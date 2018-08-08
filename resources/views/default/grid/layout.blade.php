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

<div id="{{ $grid->uniqueId() }}-container">
    @if($hasBatch)
        <div class="panel panel-default">
            <div class="panel-body" style="padding: 5px; line-height: 2.67em;">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-default lego-select-all" v-on:click="selectAll">
                        <span class="glyphicon glyphicon-check"></span> 全选
                    </button>
                    <button class="btn btn-default lego-select-toggle" v-on:click="selectReverse">
                        <span class="glyphicon glyphicon-unchecked"></span> 反选
                    </button>
                    <button class="btn btn-default" id="lego-selected-num">已选 @{{ selected }} 项</button>
                </div>

                &middot;

                <form
                    method="post"
                    style="display: inline;"
                    :action="currentBatchAction"
                    :target="currentBatchFormTarget"
                    ref="form"
                >
                    <input type="hidden" name="ids" id="lego-grid-batch-input-ids" :value="selectedIdsValue">
                    {{ csrf_field() }}

                    <button
                        v-for="(batch, name) in batches"
                        v-on:click="submitBatch(batch)"
                        class="btn btn-default btn-sm"
                        style="margin-left: 2px;"
                    >
                        <span class="glyphicon glyphicon-send"></span> @{{ name }}
                    </button>
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

@if($hasBatch)
    @push('lego-scripts')
    <script>
        $(document).ready(function () {
            lego.createGridBatch(
                '{{ $grid->uniqueId() }}-container',
                {!! json_encode($grid->pluckBatchIds()) !!},
                {!! json_encode($grid->batchesAsArray()) !!}
            )
        });
    </script>
    @endpush
@endif

@include('lego::default.snippets.bottom-buttons', ['widget' => $grid])
