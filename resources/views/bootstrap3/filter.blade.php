<?php /** @var \Lego\Set\Filter\Filter $filter */ ?>

@include('lego::bootstrap3.snippet.button-pair', ['set' => $filter, 'left' => 'LeftTop', 'right' => 'RightTop'])

<form method="get" class="form-inline lego-filter">
    @foreach($filter->getFields() as $field)
        <div class="form-group" style="{{ $field->isHiddenInput() ? 'display: none;' : '' }}">
            <label class="sr-only" for="">{{ $field->getLabel() }}</label>
            {{ $field->render() }}
        </div>
    @endforeach

    @foreach($filter->buttons()->getByPosition('Bottom') as $button)
        {{ $button->render('lego-button btn btn-default') }}
    @endforeach
</form>

@include('lego::bootstrap3.snippet.button-pair', ['set' => $filter, 'left' => 'LeftBottom', 'right' => 'RightBottom'])
