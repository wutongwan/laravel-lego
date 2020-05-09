<?php

namespace Lego\Field;

use Lego\Register\UserDefinedField;

/**
 * call through app('lego-fields').
 */
class FieldLoader
{
    protected $fields = [];

    private $buildInFields = [
        Provider\AutoComplete::class,
        Provider\CascadeSelect::class,
        Provider\Checkboxes::class,
        Provider\Date::class,
        Provider\DateRange::class,
        Provider\Datetime::class,
        Provider\DatetimeRange::class,
        Provider\Hidden::class,
        Provider\JSON::class,
        Provider\Number::class,
        Provider\NumberRange::class,
        Provider\Radios::class,
        Provider\Readonly::class,
        Provider\RichText::class,
        Provider\Select::class,
        Provider\Select2::class,
        Provider\Text::class,
        Provider\Textarea::class,
        Provider\Time::class,
        Provider\TimeRange::class,
    ];

    public function __construct()
    {
        $this->addBuildInFields();
        UserDefinedField::registerFromConfiguration();

        foreach (UserDefinedField::list() as $name => $field) {
            $this->fields[$name] = $field;
        }
    }

    private function addBuildInFields()
    {
        foreach ($this->buildInFields as $field) {
            $this->fields[class_basename($field)] = $field;
        }
    }

    public function get($name)
    {
        return $this->fields[$name];
    }

    public function all()
    {
        return $this->fields;
    }
}
