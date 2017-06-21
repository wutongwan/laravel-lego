<?php
/** @var \Lego\Widget\Grid\Grid $grid */
$paginator = $grid->paginator();
$hasBatch = boolval($grid->batches()) && $grid->batchModeEnabled();
?>

@if($grid->filter())
    {!! $grid->filter() !!}
    <br xmlns:v-on="http://www.w3.org/1999/xhtml" xmlns:v-on="http://www.w3.org/1999/xhtml"
        xmlns:v-on="http://www.w3.org/1999/xhtml">
    <h5>共找到 {{ $grid->paginator()->total() }} 条符合条件的记录</h5>
    <hr>
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

                <form :action="currentBatchAction" method="post" style="display: inline;">
                    <input type="hidden" name="ids" id="lego-grid-batch-input-ids" :value="selectedIdsValue">
                    {{ csrf_field() }}

                    <button
                        v-for="(action, name) in batches"
                        v-on:click="submitBatch(action)"
                        class="btn btn-default btn-sm"
                        style="margin-left: 2px;"
                        type="submit"
                    >
                        <span class="glyphicon glyphicon-send"></span> @{{ name }}
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table" id="{{ $grid->uniqueId() }}">
            <tr>
                @if($hasBatch)
                    <th>#</th>
                @endif
                @foreach($grid->cells() as $cell)
                    <th>{{ $cell->description() }}</th>
                @endforeach
            </tr>
            <?php /** @var \Lego\Operator\Store\Store $row */ ?>
            @foreach($paginator as $row)
                <tr>
                    @if($hasBatch && $__batch_id = $row->getKey())
                        <td>
                            <input type="checkbox" v-model="selectedIds" value="{{ $__batch_id }}">
                        </td>
                    @endif
                    @foreach($grid->cells() as $cell)
                        <td>{{ $cell->fill($row)->value() }}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>

    <div class="text-center">
        {!! $paginator->links() !!}
    </div>
</div>

@if($hasBatch)
    @push('lego-scripts')
    <script>
        lego.createGridBatch(
            '{{ $grid->uniqueId() }}-container',
            {{ $grid->getKeys()->toJson() }},
            {!! json_encode($grid->batchesAsArray()) !!}
        )
    </script>
    @endpush
@endif

@include('lego::default.snippets.bottom-buttons', ['widget' => $grid])
