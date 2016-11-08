@foreach($grid->exports() as $name => $url)
    {{ link_to($url, $name) }}
@endforeach

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