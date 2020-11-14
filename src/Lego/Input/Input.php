<?php

namespace Lego\Input;

use Lego\DataAdaptor\DataAdaptor;
use Lego\Foundation\FieldName;
use Lego\Rendering\RenderingManager;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

abstract class Input
{
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
     * 原始值
     *
     * 原始值和输入值的类型使用了 Option ，方便对是否设置过值进行判定
     *
     * @var Option
     */
    private $originalValue;

    /**
     * 输入值
     * @var Option
     */
    private $inputValue;

    final public function getOriginalValue()
    {
        if ($this->originalValue === null) {
            $this->originalValue = None::create();
            return null;
        }

        return $this->originalValue->getOrElse(null);
    }

    final public function setOriginalValue($originalValue)
    {
        $this->originalValue = new Some($originalValue);
        return $this;
    }

    final public function isOriginalValueExists(): bool
    {
        return $this->originalValue instanceof Some;
    }

    final public function getInputValue()
    {
        if ($this->inputValue === null) {
            $this->inputValue = None::create();
            return null;
        }

        return $this->inputValue->getOrElse(null);
    }

    final public function setInputValue($inputValue)
    {
        $this->inputValue = new Some($inputValue);
        return $this;
    }

    final public function isInputValueExists(): bool
    {
        return $this->inputValue instanceof Some;
    }

    /**
     * 获取当前值，如果设置过 inputValue 则返回 inputValue，否则返回 originalValue
     */
    final public function getCurrentValue()
    {
        return $this->isInputValueExists() ? $this->getInputValue() : $this->getOriginalValue();
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

    public function initializeHook()
    {
    }

    /**
     * 渲染前触发
     */
    public function beforeRenderHook(): void
    {
    }

    /**
     * 表单提交后触发
     */
    public function afterSubmitHook(): void
    {
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
     * @var DataAdaptor
     */
    private $adaptor;

    /**
     * @param DataAdaptor $adaptor
     */
    final public function setAdaptor(DataAdaptor $adaptor)
    {
        $this->adaptor = $adaptor;
        return $this;
    }

    /**
     * @return DataAdaptor
     */
    final public function getAdaptor(): DataAdaptor
    {
        return $this->adaptor;
    }

    public function render()
    {
        return app(RenderingManager::class)->render($this);
    }
}
