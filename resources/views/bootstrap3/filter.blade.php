<?php /** @var \Lego\Set\Filter\Filter $filter */ ?>

@include('lego::bootstrap3.snippet.button-pair', ['set' => $filter, 'left' => 'LeftTop', 'right' => 'RightTop'])

<form method="get" class="form-inline lego-filter-style-inline" style="line-height: 40px;">
    @foreach($filter->getFields() as $field)
        <div class="form-group" style="{{ $field->isHiddenInput() ? 'display: none;' : '' }}">
            <label class="sr-only" for="">{{ $field->getLabel() }}</label>
            {{ $field->render() }}
        </div>
    @endforeach
    <div class="form-group">
        @foreach($filter->buttons()->getByPosition('Bottom') as $button)
            {{ $button->render('lego-button btn btn-default') }}
        @endforeach
    </div>
</form>

@include('lego::bootstrap3.snippet.button-pair', ['set' => $filter, 'left' => 'LeftBottom', 'right' => 'RightBottom'])
