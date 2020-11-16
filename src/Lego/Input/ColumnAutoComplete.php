<?php

namespace Lego\Input;

class ColumnAutoComplete extends AutoComplete
{
    protected static function hooksClassName(): string
    {
        return ColumnAutoCompleteHooks::class;
    }
}
