<?php namespace Lego\Register\Data;

abstract class Data
{
    /**
     * 注册数据的分类、标记等等
     * @var string|null
     */
    protected $name = null;

    /**
     * 注册的数据
     * @var array
     */
    protected $data;

    public function __construct($data, $category = null)
    {
        $this->validate($data);

        $this->data = $data;
        $this->name = $category;
    }

    public function name()
    {
        return $this->name;
    }

    public function data($default = null)
    {
        return is_null($this->data) ? $default : $this->data;
    }

    /**
     * 校验注册的数据是否合法, 不合法时抛出异常
     * @param $data
     */
    abstract protected function validate($data);

    /**
     * 注册完成后的回调
     */
    public function afterRegistered()
    {
    }
}