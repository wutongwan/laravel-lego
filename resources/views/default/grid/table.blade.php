<?php
/** @var \Lego\Widget\Grid\Grid $grid */
$paginator = $grid->paginator();
?>

@include('lego::default.snippets.top-buttons', ['widget' => $grid])

<div class="table-responsive">
    <table class="table">
        <tr>
            @foreach($grid->cells() as $cell)
                <th>{{ $cell->description() }}</th>
            @endforeach
        </tr>
        <?php /** @var \Lego\Operator\Store\Store $row */ ?>
        @foreach($paginator as $row)
            <tr>
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
