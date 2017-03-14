<?php namespace Lego\Field\Provider;

use Lego\Field\Concerns\DisabledInFilter;

class JSON extends Text
{
    use DisabledInFilter;

    protected $jsonKey;

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $exploded = explode(':', $this->column(), 2);

        lego_assert(count($exploded) === 2, 'JSON field `name` example: `array:key:sub-key:...`');

        $this->column = $exploded[0];
        $this->jsonKey = str_replace(':', '.', $exploded[1]);
    }

    public function getOriginalValue()
    {
        return array_get($this->decode($this->originalValue), $this->jsonKey);
    }

    protected function decode($json)
    {
        return is_string($json) ? json_decode($json, JSON_OBJECT_AS_ARRAY) : $json;
    }

    protected function encode($data)
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function syncValueToStore()
    {
        $column = $this->getColumnPathOfRelation($this->column());
        $original = $this->decode($this->store->get($column));
        array_set($original, $this->jsonKey, $this->getNewValue());
        $this->store->set($column, $this->encode($original));
    }
}
