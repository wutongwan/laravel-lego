<?php

namespace Lego\Utility;

class StringUtility
{
    public static function isEmpty($string)
    {
        return strlen(trim($string)) === 0;
    }
}
