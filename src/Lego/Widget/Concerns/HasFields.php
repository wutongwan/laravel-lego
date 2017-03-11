<?php namespace Lego\Widget\Concerns;

use Illuminate\Support\Collection;

use Lego\Field\Field;
use Lego\Foundation\Event;
use Lego\Foundation\Facades\LegoFields;
use Lego\Foundation\Fields;

/**
 * Field 相关逻辑
 *
 * @mixin HasGroups
 */
trait HasFields
{
    /**
     * @var Fields|Collection
     */
    protected $fields;

    protected function initializeHasFields()
    {
        $this->fields = new Fields();

        // addField Magic call
        foreach (LegoFields::all() as $name => $class) {
            self::macro('add' . $name, function () use ($class) {
                $args = func_get_args();
                return $this->addFieldByClassName($class, $args[0], $args[1] ?? null);
            });
        }
    }

    private function addFieldByClassName($class, $name, $description = null)
    {
        /** @var Field $field */
        $field = new $class($name, $description, $this->getStore());
        $field->setElementNamePrefix($this->getFieldElementNamePrefix());
        return $this->addField($field);
    }

    /**
     * 为避免同一页面有多个控件时的
     */
    abstract protected function getFieldElementNamePrefix();

    /**
     * all fields
     *
     * @return Collection|Field[]|Fields
     */
    public function fields()
    {
        return $this->fields->fields();
    }

    /**
     * only editable fields
     *
     * @return Collection|Field[]
     */
    public function editableFields()
    {
        $ignored = [];
        foreach ($this->groups() as $group) {
            if ($group->getCondition() && $group->getCondition()->fail()) {
                $ignored = array_merge($ignored, $group->fieldNames());
            }
        }

        return $this->fields()->filter(function (Field $field) use ($ignored) {
            return $field->isEditable() && !in_array($field->name(), $ignored);
        });
    }

    /**
     * 根据 name 获取指定 Field
     * @param $fieldName
     * @return Field|null
     */
    public function field($fieldName)
    {
        return $this->fields->field($fieldName);
    }

    public function addField(Field $field): Field
    {
        $this->fields->add($field);

        Event::fire('after-add-field', [$field]);

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
