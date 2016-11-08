<form method="get" class="form-inline" style="line-height: 40px;">
    @foreach($filter->fields() as $field)
        <div class="form-group">
            <label class="sr-only" for="{{ $field->elementId() }}">{{ $field->description() }}</label>
            {{ $field->toHtmlString() }}
        </div>
    @endforeach

    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="查询">
        <a href="?" class="btn btn-default">清空</a>
    </div>
</form>