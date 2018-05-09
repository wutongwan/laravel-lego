<?php namespace Lego\Operator\Collection;

use Illuminate\Support\Collection;
use Lego\Operator\Store;

class ArrayStore extends Store
{
    public static function parse($data)
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

    public function getAssociated($attribute)
    {
        return null;
    }

    public function associate($attribute, $id)
    {
    }

    public function dissociate($attribute)
    {
    }

    public function getAttached($attribute): Collection
    {
        return new Collection();
    }

    public function attach($attribute, array $ids, array $attributes = [])
    {
    }

    public function detach($attribute, array $ids)
    {
    }
}
