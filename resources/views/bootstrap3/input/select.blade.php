<?php /** @var \Lego\Input\Select $input */ ?>
<select name="{{ $input->getInputName() }}" class="form-control">
    @if($input->isRequired() === false)
        <option value="">{{ $input->getPlaceholder() ? "* {$input->getPlaceholder()} *" : '' }}</option>
    @endif
    {!! $input->getOptionsHtml() !!}
</select>
