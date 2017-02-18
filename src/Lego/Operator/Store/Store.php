<?php namespace Lego\Operator\Store;

use Lego\Operator\Operator;

/**
 * Store 为 Lego 提供统一的读写 API
 */
abstract class Store extends Operator
{
    abstract public function get($attribute, $default = null);

    abstract public function set($attribute, $value);

    /**
     * 存储操作, 针对 Eloquent 等场景
     * @param array $options
     * @return bool
     */
    abstract public function save($options = []);

    /** 方便渲染模板、使用原数据的函数 */

    public function __get($name)
    {
        return $this->get($name);
    }

    function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->original, $name], $arguments);
    }
}
