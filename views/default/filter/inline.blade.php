<form method="get" class="form-inline">
    @foreach($filter->fields() as $field)
        <div class="form-group">
            <label class="sr-only" for="{{ $field->elementId() }}">{{ $field->description() }}</label>
            {{ $field->toHtmlString() }}
        </div>
    @endforeach

    <button type="submit" class="btn btn-primary">查询</button>
</form>