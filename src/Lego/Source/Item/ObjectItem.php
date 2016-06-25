<?php namespace Lego\Source\Item;

class ObjectItem extends Item
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Get value of $attribute
     */
    protected function getAttribute($attribute, $default = null)
    {
        // TODO: Implement getAttribute() method.
    }

    /**
     * 嵌套 get
     */
    protected function getAttributeNested(array $path, $default = null)
    {
        // TODO: Implement getAttributeNested() method.
    }

    /**
     * Set value of $attribute as $value
     */
    protected function setAttribute($attribute, $value)
    {
        // TODO: Implement setAttribute() method.
    }

    /**
     * Save Data
     */
    protected function save($options = [])
    {
        // TODO: Implement save() method.
    }
}