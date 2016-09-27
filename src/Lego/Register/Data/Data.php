<?php namespace Lego\Register\Data;

abstract class Data
{
    /**
     * 注册数据适用对象的类名
     * @var string|null
     */
    private $type = null;

    /**
     * 注册的数据
     * @var array
     */
    private $data;

    public function __construct($type = null, array $data = [])
    {
        $this->type = $type;

        $this->validate($data);

        $this->data = $data;
    }

    protected function type()
    {
        return $this->type;
    }

    public function data($attribute = null, $default = null)
    {
        if (is_null($attribute)) {
            return $this->data;
        }
        return array_get($this->data, $attribute, $default);
    }

    /**
     * @param Data|array $data
     */
    public function merge($data)
    {
        $this->data = array_merge(
            $this->data(),
            is_array($data) ? $data : $data->data()
        );
    }

    /**
     * 校验注册的数据是否合法, 不合法时抛出异常
     * @param array $data
     */
    abstract protected function validate(array $data = []);

    /**
     * 注册完成后的回调
     */
    public function afterRegistered()
    {
        // do nothing.
    }
}