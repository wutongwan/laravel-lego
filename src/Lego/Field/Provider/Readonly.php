<?php namespace Lego\Field\Provider;

use Lego\Field\Concerns\DisabledInFilter;

class Readonly extends Text
{
    use DisabledInFilter;

    protected $readonlyValue;

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $this->readonly();

        $this->readonlyValue = $this->description();
        $this->description = $this->column();
    }

    public function description()
    {
        return $this->description;
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

    public function syncValueToStore()
    {
    }
}
