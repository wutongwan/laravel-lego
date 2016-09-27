<?php namespace Lego\Source\Row;

use Lego\Source\Source;

abstract class Row extends Source
{
    protected function initialize()
    {
    }

    abstract public function get($attribute, $default = null);

    abstract public function set($attribute, $value);

    /**
     * 存储操作, 针对 Eloquent 等场景
     * @param array $options
     * @return mixed
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
        return call_user_func_array([$this->data, $name], $arguments);
    }
}