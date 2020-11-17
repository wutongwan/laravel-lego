<?php

namespace Lego\Input;

use Illuminate\Support\HtmlString;
use Lego\Foundation\FieldName;
use Lego\Foundation\Values;

abstract class Input
{
    /**
     * @var Values
     * @psalm-readonly
     */
    private $values;

    public function __construct()
    {
        $this->values = new Values();
    }

    /**
     * @return Values
     */
    final public function values(): Values
    {
        return $this->values;
    }

    /**
     * 输入框是否被禁用，一般是通过 disabled 属性实现
     *
     * eg: <input type="text" disabled="disabled" value="hello world" />
     *
     * @var bool
     */
    private $disabled = false;

    /**
     * 是否只读，不渲染输入组件，直接返回内容
     *
     * eg: <p>hello world</p>
     *
     * @var bool
     */
    private $readonly = false;

    final public function disabled(bool $disabled = true)
    {
        $this->disabled = $disabled;
        return $this;
    }

    final public function isDisabled()
    {
        return $this->disabled;
    }

    final public function readonly(bool $readonly = true)
    {
        $this->readonly = $readonly;
        return $this;
    }

    final public function isReadonly()
    {
        return $this->readonly;
    }

    final public function isInputAble()
    {
        return $this->isReadonly() === false && $this->isDisabled() === false;
    }

    /**
     * 输入框的标签
     *
     * eg:  <label>User Name</label><input name='username'> 中的 `User Name`
     *
     * @var string
     */
    private $label = '';

    /**
     * 输入框的占位符，用于展示默认值或者作为提示
     *
     * eg: <input name="country" placeholder="China"> 中的 `China`
     *
     * @var string
     */
    private $placeholder = '';

    /**
     * @return string
     */
    final public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    final public function setLabel(string $label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    final public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     */
    final public function placeholder(string $placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * 表单中的 name
     *
     * eg: <input name="username" /> 中的 `username`
     *
     * @var string
     */
    private $inputName = '';

    final public function getInputName(): string
    {
        return $this->inputName;
    }

    final public function setInputName(string $inputName)
    {
        $this->inputName = $inputName;
        return $this;
    }


    /**
     * @var FieldName
     */
    private $fieldName;

    /**
     * @return FieldName
     */
    public function getFieldName(): FieldName
    {
        return $this->fieldName;
    }

    /**
     * @return $this
     */
    public function setFieldName(FieldName $fieldName)
    {
        $this->fieldName = $fieldName;
        return $this;
    }

    /**
     * Set default value
     *
     * @param mixed $default
     * @return $this
     */
    public function default($default)
    {
        $this->values->setDefaultValue($default);
        return $this;
    }

    /**
     * 是否必填
     *
     * @var bool
     */
    private $required = false;

    public function required(bool $required = true)
    {
        $this->required = $required;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return string|HtmlString
     */
    abstract public function render();
}
