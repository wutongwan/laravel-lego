<?php
/** @var \Lego\Set\Form $form */
?>

<form method="post" class="form-horizontal" action="">
    @foreach($form->getFields() as $field)
        <div class="form-group">
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

    {{--    @if($form->isEditable())--}}
    {{ csrf_field() }}
    {{--    @endif--}}
</form>
