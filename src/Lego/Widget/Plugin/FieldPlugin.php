<?php namespace Lego\Widget\Plugin;

use Illuminate\Support\Collection;
use Lego\Field\Field;
use Lego\Field\Provider\Text;
use Lego\LegoException;

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

        return $field;
    }

    /**
     * 捕获 addXXX 函数, eg: addText($fieldName, $fieldDescription, $source)
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws LegoException
     */
    public function __call($name, $arguments)
    {
        if (starts_with($name, 'add')) {
            array_unshift($arguments, substr($name, 3));
            return call_user_func_array([$this, 'add'], $arguments);
        }

        throw new LegoException('Undefined method ' . $name);
    }

    public function eachField(\Closure $closure)
    {
        $this->fields()->each($closure);
    }

    protected function syncFieldsNewValue()
    {
        $this->eachField(function (Field $field) {
            $field->updateValue();
        });
    }
}