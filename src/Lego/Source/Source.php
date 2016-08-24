<?php namespace Lego\Source;

/**
 * Lego 数据源 接口
 */
abstract class Source
{
    /**
     * 传入的原始数据
     */
    protected $original;

    final public function load($queryOrData)
    {
        $this->original = $queryOrData;

        $this->initialize();

        return $this;
    }

    /**
     * 获取录入的原始数据对象
     * @return mixed
     */
    final public function original()
    {
        return $this->original;
    }

    abstract protected function initialize();
}