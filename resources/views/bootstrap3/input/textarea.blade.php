<?php /** @var \Lego\Input\Textarea $input */ ?>
<textarea name="{{ $input->getInputName() }}"
          class="form-control {!! $input->attributes()->getClassString() !!}"
        {!! $input->attributes()->toString(['class']) !!}>{{ $input->values()->getCurrentValue() }}</textarea>
