<?php

namespace Lego\Input;

class ColumnAutoComplete extends AutoComplete
{
    public function formInputHandler()
    {
        return Form\ColumnAutoCompleteHandler::class;
    }
}
