<?php
/** @var \Lego\Widget\Grid\Grid $grid */
$paginator = $grid->paginator();
$hasBatch = boolval($grid->batches());
?>

@include('lego::default.snippets.top-buttons', ['widget' => $grid])

@if($hasBatch)
    <div class="panel panel-default">
        <div class="panel-body" style="padding: 5px;">
            <div class="btn-group btn-group-sm">
                <a href="{{ \Lego\Register\HighPriorityResponse::exitUrl() }}" class="btn btn-default">
                    <span class="glyphicon glyphicon-log-out"></span> 退出
                </a>
                <button class="btn btn-default lego-select-all">
                    <span class="glyphicon glyphicon-check"></span> 全选
                </button>
                <button class="btn btn-default lego-select-toggle">
                    <span class="glyphicon glyphicon-unchecked"></span> 反选
                </button>
            </div>

            <div class="btn-group btn-group-sm">
                @foreach($grid->batches() as $batch)
                    <button class="btn btn-default lego-batch-button" data-action="{{ $batch->url() }}">
                        <span class="glyphicon glyphicon-send"></span> {{ $batch->name() }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="hide">
        {{-- <input type="text" name="ids[]" value=""> --}}
        <form id="lego-grid-batch-form" action="" method="post"></form>
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

            // events
            $checkboxes.on('ifToggled', function () {
                var $this = $(this);
                var id = $this.data('batch-id');
                var $form = $('#lego-grid-batch-form');
                if ($this.prop('checked')) {
                    if ($form.find('[value="' + id + '"]').length === 0) {
                        $form.prepend($('<input/>', {type: "text", name: "ids[]", value: id}));
                    }
                } else {
                    $form.find('[value="' + id + '"]').remove();
                }
            });
            $('.lego-select-all').on('click', function () {
                $checkboxes.iCheck('check');
            });

            $('.lego-select-toggle').on('click', function () {
                $checkboxes.iCheck('toggle');
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

<div style="text-align: center;">
    {!! $paginator->links() !!}
</div>

@include('lego::default.snippets.bottom-buttons', ['widget' => $grid])
