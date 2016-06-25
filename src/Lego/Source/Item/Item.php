<?php namespace Lego\Source\Item;

abstract class Item
{
    abstract public function __construct($data);

    public function get($attribute, $default = null)
    {
        $path = explode('.', $attribute);

        if (count($path) > 1) {
            return $this->getAttributeNested($path, $default);
        }

        return $this->getAttribute($attribute, $default);
    }

    /**
     * Alias to setAttribute(), 统一对外接口的命名方式
     */
    public function set($key, $value)
    {
        return $this->setAttribute($key, $value);
    }

    /**
     * Get value of $attribute
     */
    abstract protected function getAttribute($attribute, $default = null);

    /**
     * 嵌套 get
     */
    abstract protected function getAttributeNested(array $path, $default = null);

    /**
     * Set value of $attribute as $value
     */
    abstract protected function setAttribute($attribute, $value);

    /**
     * Save Data
     */
    abstract protected function save($options = []);
}