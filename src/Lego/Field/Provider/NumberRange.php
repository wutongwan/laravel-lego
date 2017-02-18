<?php namespace Lego\Field\Provider;

use Lego\Field\Concerns\BetweenFilterTrait;
use Lego\Field\Concerns\FilterOnly;

class NumberRange extends Number
{
    use BetweenFilterTrait;
    use FilterOnly;

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
    public function render()
    {
        return $this->view('lego::default.field.number-range');
    }
}
