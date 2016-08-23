<?php namespace Lego\Source\Record;

abstract class Record
{
    protected $record;

    public function load($data)
    {
        $this->record = $data;
        
        return $this;
    }

    public function record()
    {
        return $this->record;
    }

    abstract public function get($attribute, $default = null);

    abstract public function set($attribute, $value);

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
        return call_user_func_array([$this->record, $name], $arguments);
    }
}