<?php namespace Lego\Widget\Concerns;

use Illuminate\Support\Collection;

use Lego\Field\Field;
use Lego\Foundation\Facades\LegoFields;

/**
 * Field 相关逻辑
 */
trait HasFields
{
    /**
     * Field Collection
     * @var Collection
     */
    private $fields;

    protected function initializeHasFields()
    {
        $this->fields = collect([]);

        // addField Magic call
        foreach (LegoFields::all() as $name => $class) {
            self::macro('add' . $name, function () use ($class) {
                $args = func_get_args();
                /** @var Field $field */
                $field = new $class($args[0], $args[1] ?? null, $this->getStore());
                $field->setElementNamePrefix($this->getFieldElementNamePrefix());
                return $this->addField($field);
            });
        }
    }

    /**
     * 为避免同一页面有多个控件时的
     */
    abstract protected function getFieldElementNamePrefix();

    /**
     * all fields
     *
     * @return Collection|Field[]
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * only editable fields
     *
     * @return Collection|Field[]
     */
    public function editableFields()
    {
        return $this->fields->filter(function (Field $field) {
            return $field->isEditable();
        });
    }

    /**
     * 根据 name 获取指定 Field
     * @param $fieldName
     * @return Field|null
     */
    public function field($fieldName)
    {
        return $this->fields()->get($fieldName);
    }

    public function addField(Field $field): Field
    {
        $this->fields[$field->name()] = $field;

        return $field;
    }

    protected function processFields()
    {
        $this->fields()->each(function (Field $field) {
            $field->process();
        });
    }

    /**
     * Mark Fields as Required.
     *
     * @param array[]|Field[] $fields
     * @return $this
     */
    public function required($fields = [])
    {
        $fields = $fields ?: $this->fields();

        foreach ($fields as $field) {
            if (is_string($field)) {
                $this->field($field)->required();
                continue;
            }

            /** @var Field $field */
            $field->required();
        }

        return $this;
    }
}
