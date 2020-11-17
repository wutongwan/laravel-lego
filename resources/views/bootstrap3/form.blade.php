<?php
/** @var \Lego\Set\Form\Form $form */
?>

<div class="clearfix" style="margin-bottom: 5px;">
    <div class="pull-left">
        @foreach($form->buttons()->getByPosition('LeftTop') as $button)
            {{ $button->render('lego-button btn btn-default') }}
        @endforeach
    </div>
    <div class="pull-right">
        @foreach($form->buttons()->getByPosition('RightTop') as $button)
            {{ $button->render('lego-button btn btn-default') }}
        @endforeach
    </div>
</div>

<form method="post" class="form-horizontal" action="">
    @foreach($form->getFields() as $field)
        <div class="form-group" style="{{ $field->isHiddenInput() ? 'display: none;' : '' }}">
            <label for="lego-form-field-id-{{ $field->getInputName() }}" class="col-sm-2 control-label">
                @if($field->isRequired() && $field->isInputAble())
                    <span class="text-danger">*</span>
                @endif
                {{ $field->getLabel() }}
            </label>
            <div class="col-sm-10">
                {{ $field->render() }}
                @foreach($field->messages() as $msg)
                    @if($msg->isError())
                        <p class="text-danger"><i class="glyphicon glyphicon-warning-sign"></i>&nbsp;&nbsp;{{ $msg->getContent() }}</p>
                    @else
                        <p class="text-info"><i class="glyphicon glyphicon-info-sign"></i>&nbsp;&nbsp;{{ $msg->getContent() }}</p>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach
    @if($form->isEditable())
        {{ csrf_field() }}
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                @foreach($form->buttons()->getByPosition('Bottom') as $button)
                    {{ $button->render('lego-button btn btn-default') }}
                @endforeach
            </div>
        </div>
    @endif
</form>

<div class="clearfix">
    <div class="pull-left">
        @foreach($form->buttons()->getByPosition('LeftBottom') as $button)
            {{ $button->render('lego-button btn btn-default') }}
        @endforeach
    </div>
    <div class="pull-right">
        @foreach($form->buttons()->getByPosition('RightBottom') as $button)
            {{ $button->render('lego-button btn btn-default') }}
        @endforeach
    </div>
</div>
