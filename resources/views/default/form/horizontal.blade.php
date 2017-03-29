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
                <button type="submit" class="btn btn-primary">提交</button>
            </div>
        </div>
    @endif
</form>

<div id="lego-hide" class="hide"></div>

@include('lego::default.snippets.bottom-buttons', ['widget' => $form])

@push('lego-scripts')
    @include('lego::default.form.condition-group', ['groups' => $form->groups()])
@endpush
