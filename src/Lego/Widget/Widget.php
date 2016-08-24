<?php namespace Lego\Widget;

use Lego\Field\Field;
use Lego\Field\Group;
use Lego\Field\Provider\Text;
use Lego\LegoException;
use Lego\Source\Source;

/**
 * Lego中所有大型控件的基类
 */
abstract class Widget
{
    /**
     * 数据源
     * @var Source $source
     */
    private $source;

    private $fields = [];
    private $groups = [];


    public function __construct($data)
    {
        $this->source = lego_source($data);
    }

    protected function source() : Source
    {
        return $this->source;
    }

    public function fields()
    {
        return $this->fields;
    }

    protected function add($fieldType, $fieldName, $fieldDescription) : Field
    {
        // 为避免人肉拼接namespace, 所以写了下面一坨
        $delimiter = '\\';
        $namespace = join($delimiter, array_slice(explode($delimiter, Text::class), 0, -1));
        $field = $namespace . $delimiter . $fieldType;

        lego_assert(class_exists($field), 'Undefined Field ' . $field);

        /** @var Field $field */
        $field = new $field($fieldName, $fieldDescription, $this->source());

        $this->fields [$fieldName] = $field;

        return $field;
    }

    public function addGroup($groupName, \Closure $callback = null) : Group
    {
        return new Group(); // TODO
    }

    public function __call($name, $arguments)
    {
        if (starts_with($name, 'add')) {
            array_unshift($arguments, substr($name, 3));
            return call_user_func_array([$this, 'add'], $arguments);
        }

        throw new LegoException('Undefined method ' . $name);
    }

    abstract public function render() : string;
}