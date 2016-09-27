<?php namespace Lego\Widget\Plugin;

use Illuminate\Support\Collection;
use Lego\Field\Field;
use Lego\Field\Provider\Text;

/**
 * Field 相关逻辑
 * ** Magic Add **
 * @method Text addText(string $fieldName, $fieldDescription)
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

    protected function add($fieldType, $fieldName, $fieldDescription) : Field
    {
        // 为避免人肉拼接namespace, 所以写了下面一坨
        $field = class_namespace(Text::class, $fieldType);

        lego_assert(class_exists($field), 'Undefined Field ' . $field);

        /** @var Field $field */
        $field = new $field($fieldName, $fieldDescription, $this->source());

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
             * 捕获 addXXX 函数, eg: addText($fieldName, $fieldDescription, $source)
             */
            'add*' => function () {
                $arguments = func_get_args(); // eg: [addText, 'name', '描述']
                $arguments[0] = substr($arguments[0], 3); // addText => text
                return call_user_func_array([$this, 'add'], $arguments);
            }
        ];
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