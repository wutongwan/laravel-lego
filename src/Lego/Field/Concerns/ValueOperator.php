<?php namespace Lego\Field\Concerns;

trait ValueOperator
{
    /**
     * 原始值，一般读取自数据库等数据源
     */
    protected $originalValue;

    /**
     * 新值，一般为当前请求中产生的值
     */
    protected $newValue;

    /**
     * 展示值，仅用于展示
     */
    protected $displayValue;

    /**
     * 默认值
     */
    protected $defaultValue;

    public function getOriginalValue()
    {
        return $this->originalValue;
    }

    public function setOriginalValue($originalValue)
    {
        $this->originalValue = $originalValue;

        return $this;
    }

    public function getNewValue()
    {
        return $this->newValue;
    }

    public function setNewValue($value)
    {
        $this->newValue = $value;

        return $this;
    }

    public function getDisplayValue()
    {
        return $this->displayValue;
    }

    public function setDisplayValue($displayValue)
    {
        $this->displayValue = $displayValue;

        return $this;
    }

    public function default($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * 上方是标准 getter & setter
     * 下方是特定场景的 getter
     */

    /**
     * 输入框中显示给用户的值
     */
    public function takeDefaultInputValue()
    {
        return lego_default(
            $this->getNewValue(),
            $this->getDefaultValue(),
            $this->getOriginalValue()
        );
    }
}
