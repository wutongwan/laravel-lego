<?php /** @var \Lego\Input\Text $input */ ?>
<input type="text" name="{{ $input->getInputName() }}" value="{{ $input->values()->getCurrentValue() }}"
       class="form-control {!! $input->attributes()->getClassString() !!}"
    {!! $input->attributes()->toString(['type', 'name', 'value', 'class']) !!}>
