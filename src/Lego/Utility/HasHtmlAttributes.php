<?php namespace Lego\Utility;

trait HasHtmlAttributes
{
    /**
     * Html element attributes.
     * @var array
     */
    protected $attributes = [];

    /**
     * 设置 html 属性
     *
     * 第一个参数类型可以为字符串或数组
     *      - 数组, 将merge到现有attributes中
     *      - 字符串, 和对应的 value 放入 attributes
     *
     * @param array|string $attribute
     * @param string|null $value
     * @return $this
     */
    public function setAttribute($attribute, $value = null)
    {
        if (is_array($attribute)) {
            $this->attributes = array_merge_recursive($this->attributes, $attribute);
            return $this;
        }

        if (is_array($value)) {
            $this->attributes[$attribute] = array_merge((array)$this->attributes[$attribute] ?? [], $value);
        } else {
            $this->attributes[$attribute] = $value;
        }

        return $this;
    }

    public function getAttribute($attribute, $default = null)
    {
        return array_get($this->attributes, $attribute, $default);
    }

    public function getAttributeString($attribute, $default = '')
    {
        $values = $this->getAttribute($attribute);
        return is_null($values) ? $default : join(' ', (array)$values);
    }

    public function removeAttribute($attribute)
    {
        foreach (func_get_args() as $attr) {
            unset($this->attributes[$attr]);
        }
        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttributesString()
    {
        return HtmlUtility::renderAttributes($this->attributes);
    }
}
