@include('lego::default.snippets.top-buttons', ['widget' => $filter])

<?php /** @var \Lego\Widget\Filter $filter */ ?>
<form method="get"
      class="form-inline {{ isset($__gridSystem) ? 'lego-filter-style-inline' : '' }}"
      style="line-height: 40px;" id="{{ $filter->uniqueId() }}">
    @foreach($filter->fields() as $field)
        <div class="form-group">
            <label class="sr-only" for="{{ $field->elementId() }}">{{ $field->description() }}</label>
            {{ $field->toHtmlString() }}
        </div>
    @endforeach

    <div class="form-group">
        @foreach($filter->getBottomButtons() as $button)
            {!! $button !!}
        @endforeach
    </div>
</form>

@include('lego::default.snippets.bottom-buttons', ['widget' => $filter])
