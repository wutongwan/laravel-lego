@include('lego::default.snippets.top-buttons', ['widget' => $grid])

<div class="table-responsive">
    <table class="table">
        <tr>
            @foreach($grid->fields() as $field)
                <th>{{ $field->description() }}</th>
            @endforeach
        </tr>
        @foreach($grid->rows() as $row)
            <tr>
                @foreach($grid->fields() as $field)
                    <td>{{ $row->get($field->name()) }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
</div>

<nav aria-label="Page navigation" style="text-align: center;">
    <ul class="pagination">
        <li>
            <a href="#" aria-label="Previous">
                上一页
            </a>
        </li>
        <li><a href="#">1</a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li>
            <a href="#" aria-label="Next">
                下一页
            </a>
        </li>
    </ul>
</nav>

@include('lego::default.snippets.bottom-buttons', ['widget' => $grid])