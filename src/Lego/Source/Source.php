<?php namespace Lego\Source;

/**
 * Lego 数据源 接口
 */
abstract class Source
{
    /**
     * 传入的原始数据
     */
    protected $data;

    final public function load($queryOrData)
    {
        $this->data = $queryOrData;

        $this->initialize();

        return $this;
    }

    /**
     * 获取录入的原始数据对象
     * @return mixed
     */
    final public function data()
    {
        return $this->data;
    }

    abstract protected function initialize();
}