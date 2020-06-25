<?php

namespace Lego\Widget\Grid;

use Lego\Foundation\Exceptions\LegoException;
use Lego\Operator\Store;

class FormatTool
{
    public static function format($value, string $format, Store $store)
    {
        $initial = [
            'output' => '',
            'open' => false,
            'variable' => '',
        ];
        $result = array_reduce(
            mb_str_split($format),
            function ($carry, $char) use ($value, $store, $format) {
                switch ($char) {
                    case '{':
                        if ($carry['open'] === true) {
                            throw new LegoException("Not support nested format: $format");
                        }
                        $carry['open'] = true;
                        break;

                    case ($char === '}') && ($carry['open'] === true):
                        $carry['output'] .= ($carry['variable'] === '' ? $value : $store->get($carry['variable']));
                        $carry['open'] = false;
                        $carry['variable'] = '';
                        break;

                    case $carry['open'] === true:
                        $carry['variable'] .= $char;
                        break;

                    default:
                        $carry['output'] .= $char;
                }

                return $carry;
            },
            $initial
        );

        return $result['output'];
    }
}
