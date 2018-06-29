<?php

namespace Lego\Utility;

use Illuminate\Support\HtmlString;

class HtmlUtility
{
    /**
     * 将属性数组转换为字符串, 支持传入多个数组, 会进行递归合并, 统一属性值会通过空格拼接在一起。
     *
     * @return array
     */
    public static function mergeAttributes()
    {
        $attributes = call_user_func_array('array_merge_recursive', func_get_args());

        foreach ($attributes as $attribute => &$value) {
            if (is_array($value)) {
                $value = join(' ', array_flatten($value));
            }
        }

        return $attributes;
    }

    // 将属性列表渲染成 HTML 属性字符串
    public static function renderAttributes($attributes)
    {
        $attributes = self::mergeAttributes($attributes);

        $html = '';
        foreach ($attributes as $key => $value) {
            $html .= " {$key}=\"{$value}\"";
        }

        return new HtmlString(trim($html));
    }
}
