<?php namespace Lego\Field\Concerns;

use Lego\Field\Field;

/**
 * Class HtmlOperator
 * @package Lego\Field\Plugin
 */
trait HtmlOperator
{
    /**
     * <input type="__THIS_VALUE__" ...
     *
     * @var string
     */
    protected $inputType = 'text';

    private $elementNamePrefix;
    private $elementName;

    protected function initializeHtmlOperator()
    {
        $this->elementName = str_replace(['.', ':'], '_', $this->name());
    }

    public function elementId()
    {
        return 'lego-' . $this->elementName;
    }

    public function elementName()
    {
        return $this->elementNamePrefix . $this->elementName;
    }

    public function setElementNamePrefix($prefix)
    {
        $this->elementNamePrefix = $prefix;

        return $this;
    }

    public function getInputType()
    {
        return $this->inputType;
    }

    /**
     * 设置 Field 的 html 属性
     */
    public function attr($attributeOrAttributes, $value = null)
    {
        return $this->setAttribute($attributeOrAttributes, $value);
    }

    public function placeholder($placeholder = null)
    {
        return $this->setAttribute('placeholder', $placeholder);
    }

    public function getPlaceholder($default = null)
    {
        return $this->getAttributeString('placeholder', $default);
    }

    public function note($message)
    {
        return $this->hint($message);
    }

    public function hint($message)
    {
        /** @var Field $this */
        $this->messages()->add('note', $message);

        return $this;
    }
}
