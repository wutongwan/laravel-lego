<?php namespace Lego\Widget\Plugin;

use Illuminate\Support\Collection;

use Lego\Field\Field;
use Lego\Register\Data\Field as FieldRegister;

/**
 * Field 相关逻辑
 */
trait FieldPlugin
{
    /**
     * Field Collection
     * @var Collection
     */
    private $fields;

    protected function initializeFieldPlugin()
    {
        $this->fields = collect([]);
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

    protected function add($fieldType, $fieldName, $fieldDescription): Field
    {
        $class = FieldRegister::get($fieldType);
        /** @var Field $field */
        $field = new $class($fieldName, $fieldDescription, $this->data());

        $this->fields [$fieldName] = $field;

        $this->fieldAdded($field);

        return $field;
    }

    /**
     * @param Field $field
     */
    abstract protected function fieldAdded(Field $field);

    protected function registerFieldPluginMagicCall()
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
            $field->source()->set(
                $field->column(),
                $field->value()->current()
            );
        });
    }
}