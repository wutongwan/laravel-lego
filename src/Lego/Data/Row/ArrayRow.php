<?php namespace Lego\Data\Row;

class ArrayRow extends Row
{
    public function get($attribute, $default = null)
    {
        return data_get($this->original, $attribute, $default);
    }

    public function set($attribute, $value)
    {
        data_set($this->original, $attribute, $value);
    }

    /**
     * 存储操作, 针对 Eloquent 等场景
     * @param array $options
     * @return bool
     */
    public function save($options = []): bool
    {
        return true;
    }
}