<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Field\Concerns\DisabledInFilter;
use Lego\Field\Field;

class JSON extends Field
{
    use DisabledInFilter;

    protected $jsonKey;

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $exploded = explode(':', $this->name(), 2);

        lego_assert(count($exploded) === 2, 'JSON field `name` example: `array:key:sub-key:...`');

        $this->column = $exploded[0];
        $this->jsonKey = str_replace(':', '.', $exploded[1]);
    }

    public function setOriginalValue($originalValue)
    {
        $array = $this->decode($originalValue);
        $this->originalValue = array_get($array, $this->jsonKey);
    }

    public function setNewValue($value)
    {
        $this->newValue = $this->decode($value);

        return $this;
    }

    private function decode($json)
    {
        return is_string($json) ? json_decode($json, JSON_OBJECT_AS_ARRAY) : $json;
    }

    protected function encode($data)
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
        $this->setDisplayValue(
            $this->encode($this->getNewValue())
        );
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render()
    {
        return FormFacade::input('text', $this->elementName(), $this->getDisplayValue(), [
            'id' => $this->elementId(),
            'class' => 'form-control'
        ]);
    }

    public function syncValueToStore()
    {
        $original = $this->store->get($this->column());
        $original = is_string($original) ? $this->decode($original) : $original;
        array_set($original, $this->jsonKey, $this->getNewValue());
        $this->store->set($this->column(), $this->encode($original));
    }
}
