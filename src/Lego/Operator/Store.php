<?php

namespace Lego\Operator;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/**
 * Store 为 Lego 提供统一的读写 API.
 */
abstract class Store extends Operator implements Arrayable, \ArrayAccess
{
    public function getKeyName()
    {
        return 'id';
    }

    public function getKey()
    {
        return $this->get($this->getKeyName());
    }

    abstract public function get($attribute, $default = null);

    abstract public function set($attribute, $value);

    /**
     * 当前关联数据.
     *
     * @param $attribute
     *
     * @return Store|null
     */
    abstract public function getAssociated($attribute);

    // 创建一对一关联
    abstract public function associate($attribute, $id);

    // 解除一对一关联
    abstract public function dissociate($attribute);

    /**
     * 当前关联数据.
     *
     * @param $attribute
     *
     * @return Collection|Store[]
     */
    abstract public function getAttached($attribute): Collection;

    // 创建多对多关联
    abstract public function attach($attribute, array $ids, array $attributes = []);

    // 解除多对多关联
    abstract public function detach($attribute, array $ids);

    /**
     * 存储操作, 针对 Eloquent 等场景.
     *
     * @param array $options
     *
     * @return bool
     */
    abstract public function save($options = []);

    /** 方便渲染模板、使用原数据的函数 */
    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->get($name);
    }

    public function __unset($name)
    {
        $this->set($name, null);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->original, $name], $arguments);
    }

    public function offsetExists($offset)
    {
        return $this->get($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }
}
