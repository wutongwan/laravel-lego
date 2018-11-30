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

        $attributeStringList = [];
        foreach ($attributes as $key => $value) {
            if (is_numeric($key)) {
                $attributeStringList [] = $value;
                continue;
            }

            // Treat boolean attributes as HTML properties
            if (is_bool($value) && $key !== 'value') {
                $attributeStringList [] = $value ? $key : '';
                continue;
            }

            if (!is_null($value)) {
                $attributeStringList[] = $key . '="' . e($value) . '"';
                continue;
            }
        }

        return new HtmlString(
            join(' ', $attributeStringList)
        );
    }

    public static function input($type, $name, $value = null, array $attributes = [])
    {
        if (isset($attributes['type'])) {
            $attributes['type'] = $type;
        }

        if (isset($attributes['name'])) {
            $attributes['name'] = $name;
        }

        if (isset($attributes['value'])) {
            $attributes['value'] = $value;
        }

        return new HtmlString(
            '<input ' . self::renderAttributes($attributes) . '>'
        );
    }
}
