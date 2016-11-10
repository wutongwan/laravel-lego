<?php namespace Lego\Widget\Operators;

use Illuminate\Support\Collection;

use Lego\Field\Field;
use Lego\Register\Data\Field as FieldRegister;

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
        foreach (FieldRegister::availableFields() as $name => $class) {
            self::macro(
                'add' . $name,
                function () use ($class) {
                    return call_user_func_array(
                        [$this, 'addField'], array_merge([$class], func_get_args())
                    );
                }
            );
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

    protected function addField($class, $fieldName, $fieldDescription = null): Field
    {
        $field = new $class($fieldName, $fieldDescription, $this->data());

        $this->fields [$fieldName] = $field;

        $this->fieldAdded($field);

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
            $field->process();
        });
    }

    protected function syncFieldsValue()
    {
        $this->fields()->each(function (Field $field) {
            $field->syncCurrentValueToSource();
        });
    }
}