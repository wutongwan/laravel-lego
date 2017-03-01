<?php
/** @var \Lego\Widget\Grid\Grid $grid */
$paginator = $grid->paginator();
$hasBatch = boolval($grid->batches()) && $grid->batchModeEnabled();
?>

@if($grid->filter())
    {!! $grid->filter() !!}
    <br>
    <h5>共找到 {{ $grid->paginator()->total() }} 条符合条件的记录</h5>
    <hr>
@endif

@include('lego::default.snippets.top-buttons', ['widget' => $grid])

@if($hasBatch)
    <div class="panel panel-default">
        <div class="panel-body" style="padding: 5px;">
            <div class="btn-group btn-group-sm">
                <button class="btn btn-default lego-select-all">
                    <span class="glyphicon glyphicon-check"></span> 全选
                </button>
                <button class="btn btn-default lego-select-toggle">
                    <span class="glyphicon glyphicon-unchecked"></span> 反选
                </button>
                <button class="btn btn-default" id="lego-selected-num">已选 0 项</button>
            </div>

            &middot;

            @foreach($grid->batches() as $batch)
                <button class="btn btn-default btn-sm lego-batch-button" data-batch-action="{{ $batch->url() }}">
                    <span class="glyphicon glyphicon-send"></span> {{ $batch->name() }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="hide">
        <form id="lego-grid-batch-form" action="" method="post">
            <input type="text" name="ids" id="lego-grid-batch-input-ids" value="">
            {{ csrf_field() }}
        </form>
    </div>

    @push('lego-scripts')
    <script>
        $(document).ready(function () {
            var $checkboxes = $('.lego-batch-checkbox');

            // active checkbox
            $checkboxes.iCheck({
                checkboxClass: 'icheckbox_square-blue',
                increaseArea: '20%'
            });

            var $input = $('#lego-grid-batch-input-ids');

            var getIds = function () {
                return $input.val().split(',').filter(function (el) {
                    return el.length !== 0
                })
            };

            var freshNum = function () {
                $('#lego-selected-num').text(
                    '已选 ' + getIds().length + ' 项'
                );
            };

            // events
            $checkboxes.on('ifToggled', function () {
                var $this = $(this);
                var id = $this.data('batch-id');
                var ids = getIds();
                if ($this.prop('checked')) {
                    ids.push(id);
                } else {
                    var index = ids.indexOf(id);
                    if (index) {
                        ids.splice(index, 1);
                    }
                }
                $input.val(ids.join(','));
                freshNum();
            });
            $('.lego-select-all').on('click', function () {
                $checkboxes.iCheck('check');
                freshNum();
            });
            $('.lego-select-toggle').on('click', function () {
                $checkboxes.iCheck('toggle');
                freshNum();
            });

            $('.lego-batch-button').on('click', function () {
                if (getIds().length === 0) {
                    alert('尚未选中任何记录！');
                    return;
                }
                var $form = $('#lego-grid-batch-form');
                $form.attr('action', $(this).data('batch-action'));
                $form.submit();
            });
        });
    </script>
    @endpush
@endif

<div class="table-responsive">
    <table class="table">
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
                        <input type="checkbox" class="lego-batch-checkbox" data-batch-id="{{ $__batch_id }}">
                    </td>
                @endif
                @foreach($grid->cells() as $cell)
                    <td>{{ $cell->copy()->fill($row)->value() }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
</div>

<div class="text-center">
    {!! $paginator->links() !!}
</div>

@include('lego::default.snippets.bottom-buttons', ['widget' => $grid])
