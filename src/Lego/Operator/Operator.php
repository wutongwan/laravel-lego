<?php namespace Lego\Operator;

/**
 * 数据操作员的基类
 *
 * 用途：Lego 接受的原始数据可能为 Eloquent、数组、对象等等很多类型，
 *      为兼容不同数据类需要统一的 API ，操作员即用来提供统一的 API 接口
 *
 */
abstract class Operator
{
    /**
     * Lego 接收到原始数据 $data 时，会顺序调用已注册 Operator 子类的此函数，
     *  当前类能处理该类型数据，则返回实例化后的 Operator ;
     *  反之 false ，返回 false 时，继续尝试下一个 Operator 子类
     *
     * @param $data
     * @return static|false
     */
    abstract public static function attempt($data);

    protected $data;

    final public function __construct($data)
    {
        $this->data = $data;

        $this->initialize();
    }

    protected function initialize()
    {
    }

    public function getOriginalData()
    {
        return $this->data;
    }
}
