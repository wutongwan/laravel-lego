<?php namespace Lego\Field\Provider;

use Lego\Field\Field;
use Lego\Field\Operators\DisabledInFilter;

class Readonly extends Field
{
    use DisabledInFilter;

    protected $readonlyValue;

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
        $this->readonlyValue = $this->description;
        $this->description = $this->column();
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render()
    {
        $this->setDisplayValue($this->readonlyValue);
        return parent::renderReadonly();
    }

    public function syncValueToSource()
    {
    }
}
