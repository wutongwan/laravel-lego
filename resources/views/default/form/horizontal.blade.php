<?php /** @var \Lego\Widget\Form $form */ ?>

@include('lego::default.snippets.top-buttons', ['widget' => $form])
@include('lego::default.messages', ['object' => $form])

<form id="{{ $form->elementId() }}" method="{{ $form->getMethod() }}" class="form-horizontal"
      action="{{ $form->getAction() }}">
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

{{-- 动态输入组 --}}
<div id="lego-hide" class="hide"></div>
@foreach($form->groups() as $group)
    <?php /* @var \Lego\Field\Group $group */?>
    @if($group->getCondition())
        <div style="display: none">
            @foreach($group->fields() as $target)
                <div class="lego-condition-group"
                     data-form="{{ $form->elementId() }}"
                     data-field="{{ $group->getCondition()->field()->elementName() }}"
                     data-operator="{{ $group->getCondition()->operator() }}"
                     data-expected="{{ rawurlencode(json_encode($group->getCondition()->expected())) }}"
                     data-target="{{ $target->elementName() }}"
                ></div>
            @endforeach
        </div>
    @endif
@endforeach
