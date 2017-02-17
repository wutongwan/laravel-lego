<?php namespace Lego\Field\Concerns;

trait ValueOperator
{
    /**
     * 原始值，一般读取自数据库等数据源
     */
    protected $originalValue;

    /**
     * 当前值，一般为当前请求中产生的值
     */
    protected $currentValue;

    /**
     * 展示值，仅用于展示
     */
    protected $displayValue;

    /**
     * 默认值
     */
    protected $defaultValue;

    public function getOriginalValue($default = null)
    {
        return lego_default($this->originalValue, $default);
    }

    public function setOriginalValue($originalValue)
    {
        $this->originalValue = $originalValue;

        return $this;
    }

    public function getCurrentValue($default = null)
    {
        return lego_default($this->currentValue, $default);
    }

    public function setCurrentValue($value)
    {
        $this->currentValue = $value;

        return $this;
    }

    public function value($default = null)
    {
        return lego_default($this->currentValue, $this->originalValue, $default);
    }

    public function getDisplayValue($default = null)
    {
        return lego_default($this->displayValue, $default);
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

    public function getDefaultValue($default = null)
    {
        return lego_default($this->defaultValue, $default);
    }
}
