<?php namespace Lego\Widget\Concerns;

use Illuminate\Support\Collection;

use Lego\Field\Field;
use Lego\Foundation\Fields;

/**
 * Field 相关逻辑
 */
trait FieldOperator
{
    /**
     * Field Collection
     * @var Collection
     */
    private $fields;

    protected function initializeFieldOperator()
    {
        $this->fields = collect([]);

        // addField Magic call
        foreach (app(Fields::class)->all() as $name => $class) {
            self::macro('add' . $name, function () use ($class) {
                $args = func_get_args();
                $field = new $class($args[0], $args[1] ?? null, $this->data());
                return $this->addField($field);
            });
        }
    }

    public function fields()
    {
        return $this->fields;
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

    protected function addField(Field $field): Field
    {
        $this->fields[$field->name()] = $field;

        return $field;
    }

    /**
     * @param Field $field
     */
    abstract protected function fieldAdded(Field $field);

    protected function registerFieldOperatorMagicCall()
    {
        return [
            /**
             * 捕获 addXXX 函数, eg: addText($fieldName, $fieldDescription, $data)
             */
            'add*' => function () {
                $arguments = func_get_args(); // eg: [addText, 'name', '描述']
                $arguments[0] = substr($arguments[0], 3); // addText => text
                return call_user_func_array([$this, 'add'], $arguments);
            }
        ];
    }

    protected function processFields()
    {
        $this->fields()->each(function (Field $field) {
            $this->fieldAdded($field);

            $field->process();
        });
    }

    /**
     * sync field's value to source.
     */
    protected function syncFieldsValue()
    {
        $this->fields()->each(function (Field $field) {
            $field->syncValueToSource();
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
