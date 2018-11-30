<?php

// zhangwei@dankegongyu.com

namespace Lego\Tests\Utility;

use Box\Spout\Common\Helper\GlobalFunctionsHelper;

class ExcelSpoutGlobalFunctionsHelper extends GlobalFunctionsHelper
{
    protected static $ins;

    public static function instance()
    {
        if (!self::$ins) {
            self::$ins = new self();
        }

        return self::$ins;
    }

    public $data = [
        'header' => [],
    ];

    public function header($string)
    {
        $this->data['header'][] = $string;
    }
}
