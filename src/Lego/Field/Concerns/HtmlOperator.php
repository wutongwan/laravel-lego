<?php namespace Lego\Field\Concerns;

use Illuminate\Support\Facades\Config;
use Lego\Field\Field;
use Lego\Utility\HtmlUtility;

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

    /**
     * Html element attributes.
     * @var array
     */
    private $attributes = [];

    private $elementNamePrefix;
    private $elementName;

    protected function initializeHtmlOperator()
    {
        $this->elementName = str_replace(['.', ':'], '_', $this->name());
        $this->attr([
            'id' => $this->elementId(),
            'name' => $this->elementName(),
        ]);
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
     * 获取配置文件中配置的属性列表
     * @return array
     */
    public function getConfiguredAttributes()
    {
        return Config::get('lego.field.attributes', []);
    }

    /**
     * 供前端使用的
     * @param array $merge 在子类中重写时, 方便merge widget中的属性
     * @return array
     */
    public function getMetaAttributes($merge = [])
    {
        return HtmlUtility::mergeAttributes($merge, [
            'lego-field-mode' => $this->mode
        ]);
    }

    /**
     * 获取所有属性
     * @return array
     */
    public function getAttributes()
    {
        return HtmlUtility::mergeAttributes(
            $this->getConfiguredAttributes(),
            $this->getMetaAttributes(),
            $this->attributes
        );
    }

    /**
     * 设置 Field 的 html 属性
     *
     * 第一个参数类型可以为字符串或数组
     *      - 数组, 将merge到现有attributes中
     *      - 字符串, 和对应的 value 放入 attributes
     *
     * @param array|string $attributeOrAttributes
     * @param string|null $value
     * @return $this
     */
    public function attr($attributeOrAttributes, $value = null)
    {
        if (is_array($attributeOrAttributes)) {
            $this->attributes = array_merge($this->attributes, $attributeOrAttributes);
            return $this;
        }

        $this->attributes [$attributeOrAttributes] = $value;
        return $this;
    }

    public function getAttribute($attribute, $default = null)
    {
        return array_get($this->getAttributes(), $attribute, $default);
    }

    public function placeholder($placeholder = null)
    {
        return $this->attr('placeholder', $placeholder);
    }

    public function getPlaceholder($default = null)
    {
        return $this->getAttribute('placeholder', $default);
    }

    public function note($message)
    {
        /** @var Field $this */
        $this->messages()->add('note', $message);

        return $this;
    }
}
