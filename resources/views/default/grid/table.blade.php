@include('lego::default.snippets.top-buttons', ['widget' => $grid])

<? $paginator = $grid->paginator() ?>

<div class="table-responsive">
    <table class="table">
        <tr>
            @foreach($grid->fields() as $field)
                <th>{{ $field->description() }}</th>
            @endforeach
        </tr>
        @foreach($paginator as $row)
            <tr>
                @foreach($grid->fields() as $field)
                    <td>{{ $row->get($field->name()) }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
</div>

<div style="text-align: center;">
    {!! $paginator->links() !!}
</div>

@include('lego::default.snippets.bottom-buttons', ['widget' => $grid])