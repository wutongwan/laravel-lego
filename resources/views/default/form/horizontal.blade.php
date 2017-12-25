<?php /** @var \Lego\Widget\Form $form */ ?>

@include('lego::default.snippets.top-buttons', ['widget' => $form])

@include('lego::default.messages', ['object' => $form])
<form id="{{ $form->elementId() }}" method="post" class="form-horizontal" action="{{ $form->getAction() }}">
    @foreach($form->fields() as $field)
        @include('lego::default.form.horizontal-form-group', ['field' => $field])
    @endforeach

    @if($form->isEditable())
        {{ csrf_field() }}

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                @foreach($form->getBottomButtons() as $button)
                    {!! $button !!}
                @endforeach
            </div>
        </div>
    @endif
</form>

@include('lego::default.snippets.bottom-buttons', ['widget' => $form])

@push('lego-scripts')
    @include('lego::default.form.condition-group', ['form' => $form])
@endpush
