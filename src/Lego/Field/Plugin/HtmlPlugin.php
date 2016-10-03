<?php namespace Lego\Field\Plugin;

use Lego\Field\Field;
use Lego\Helper\HtmlUtility;

/**
 * Class HtmlPlugin
 * @package Lego\Field\Plugin
 */
trait HtmlPlugin
{
    /**
     * Html element attributes.
     * @var array
     */
    private $attributes = [];

    public function elementId()
    {
        return $this->name();
    }

    public function elementName()
    {
        return $this->name();
    }

    /**
     * 获取配置文件中配置的属性列表
     * @return array
     */
    public function getConfiguredAttributes()
    {
        return config('lego.field.attributes', []);
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
        if (!array_key_exists('placeholder', $this->attributes)) {
            $this->attr('placeholder', $this->description());
        }

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
     * @param array|string $attributesOrAttributes
     * @param string|null $value
     * @return $this
     */
    public function attr($attributesOrAttributes, $value = null)
    {
        if (is_array($attributesOrAttributes)) {
            $this->attributes = array_merge($this->attributes, $attributesOrAttributes);
            return $this;
        }

        $this->attributes [$attributesOrAttributes] = $value;
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
        return $this->messages()->add('note', $message);
    }
}