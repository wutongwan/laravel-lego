<?php namespace Lego\Operator\Store;

class ArrayStore extends Store
{
    public static function attempt($data)
    {
        if (is_array($data)) {
            return new self($data);
        }

        return false;
    }

    public function get($attribute, $default = null)
    {
        return data_get($this->data, $attribute, $default);
    }

    public function set($attribute, $value)
    {
        data_set($this->data, $attribute, $value);
    }

    public function save($options = [])
    {
        return true;
    }
}
