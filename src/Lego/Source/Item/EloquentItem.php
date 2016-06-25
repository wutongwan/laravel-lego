<?php namespace Lego\Source\Item;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EloquentItem extends Item
{
    /** @var Eloquent $data */
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function setAttribute($attribute, $value)
    {
        $this->data->{$attribute} = $value;
    }

    public function getAttribute($attribute, $default = null)
    {
        return $this->data->getAttribute($attribute);
    }

    /**
     * 嵌套 get
     * @param array $path
     * @param null $default
     * @return mixed
     */
    protected function getAttributeNested(array $path, $default = null)
    {
        $object = $this->data;
        foreach ($path as $key) {
            if (!$object) {
                break;
            }
            $object = $object->getAttribute($key);
        }
        return $object;
    }

    /**
     * Save Data
     */
    protected function save($options = [])
    {
        return $this->data->save();
    }
}