<?php namespace Lego\Operator\Collection;

/**
 * 这个的兼容性比较暴力，推荐放到 STORE_LIST 的最底部
 */
class ObjectStore extends ArrayStore
{
    public static function parse($data)
    {
        return is_object($data) ? new self($data) : false;
    }

    public function get($attribute, $default = null)
    {
        return data_get($this->data, $attribute, $default);
    }

    public function set($attribute, $value)
    {
        data_set($this->data, $attribute, $value);
    }
}
