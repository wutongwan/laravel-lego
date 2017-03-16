<?php namespace Lego\Operator\Store;

class ArrayStore extends Store
{
    public static function attempt($data)
    {
        return is_array($data) ? new self($data) : false;
    }

    public function get($attribute, $default = null)
    {
        return array_get($this->data, $attribute, $default);
    }

    public function set($attribute, $value)
    {
        array_set($this->data, $attribute, $value);
    }

    public function save($options = [])
    {
        return true;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return (array)$this->data;
    }
}
