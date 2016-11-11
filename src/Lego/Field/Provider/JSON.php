<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Field\Field;
use Lego\Data\Table\Table;

class JSON extends Field
{
    protected $jsonKey;

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $exploded = explode(':', $this->name(), 2);

        lego_assert(count($exploded) === 2, 'JSON field name example: `array:key:sub-key:...`');

        $this->column = $exploded[0];
        $this->jsonKey = str_replace(':', '.', $exploded[1]);
    }

    public function getOriginalValue()
    {
        return array_get($this->value()->original(), $this->jsonKey);
    }

    public function getCurrentValue()
    {
        $original = $this->value()->original();
        $current = $this->value()->current();
        if ($current === $original) {
            return $this->getOriginalValue();
        }

        return is_string($current)
            ? json_decode($current, JSON_OBJECT_AS_ARRAY)
            : $current;
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
        $this->value()->setShow(function () {
            $value = $this->getCurrentValue();
            return is_scalar($value) ? $value : json_encode($this->getCurrentValue(), JSON_UNESCAPED_UNICODE);
        });
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render(): string
    {
        return FormFacade::input('text', $this->elementName(),
            $this->value()->show(),
            [
                'id' => $this->elementId(),
                'class' => 'form-control'
            ]
        );
    }

    public function syncCurrentValueToSource()
    {
        $original = $this->source()->get($this->column());
        array_set($original, $this->jsonKey, $this->getCurrentValue());
        $this->source()->set($this->column(), $original);
    }

    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Table $query
     * @return Table
     */
    public function filter(Table $query): Table
    {
        return $query;
    }
}