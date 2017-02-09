<?php namespace Lego\Field\Operators;

trait ValueOperator
{
    /**
     * 原始值，一般读取自数据库等数据源
     */
    protected $originalValue;
    /**
     * 当前值，一般为当前请求中产生的值
     */
    protected $value;
    /**
     * 展示值，仅用于展示
     */
    protected $displayValue;

    public function getOriginalValue($default = null)
    {
        return lego_default($this->originalValue, $default);
    }

    public function setOriginalValue($originalValue)
    {
        $this->originalValue = $originalValue;

        return $this;
    }

    public function getValue($default = null)
    {
        return lego_default($this->value, $default);
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function valueNew($default = null)
    {
        return lego_default($this->value, $default, $this->originalValue);
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
}
