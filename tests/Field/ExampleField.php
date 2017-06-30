<?php namespace Lego\Tests\Field;

use Lego\Field\Field;

class ExampleField extends Field
{
    /**
     * 数据处理逻辑
     */
    public function process()
    {
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render()
    {
        return $this->takeInputValue();
    }
}
