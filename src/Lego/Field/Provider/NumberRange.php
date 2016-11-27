<?php namespace Lego\Field\Provider;

use Lego\Field\Operators\BetweenFilterTrait;
use Lego\Field\Operators\ForFilterOnly;

class NumberRange extends Number
{
    use BetweenFilterTrait;
    use ForFilterOnly;

    /**
     * 数据处理逻辑
     */
    public function process()
    {
    }

    /**
     * 初始化对象
     */
    protected function initialize()
    {
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render(): string
    {
        return $this->view('lego::default.field.number-range');
    }
}