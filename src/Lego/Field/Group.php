<?php namespace Lego\Field;

use Lego\Field\Concerns\ConditionOfGroup;
use Lego\Foundation\Fields;

class Group
{
    use ConditionOfGroup;

    protected $fields;

    protected $name; // Group Name

    protected $fieldNames = []; // field name list belongs to group

    function __construct(Fields $fields, $name)
    {
        $this->fields = $fields;
        $this->name = $name;
    }

    public function name()
    {
        return $this->name;
    }

    public function fieldNames()
    {
        return $this->fieldNames;
    }

    public function fields()
    {
        return $this->fields
            ->filter(function (Field $field) {
                return isset($this->fieldNames[$field->name()]);
            });
    }

    public function add($field)
    {
        if (is_array($field)) {
            return $this->addMany($field);
        }

        if (func_num_args() > 1) {
            return $this->addMany(func_get_args());
        }

        if ($field instanceof Field) {
            $this->fieldNames[$field->name()] = $field->name();
        } else {
            $this->fieldNames[$field] = $field;
        }
        return $this;
    }

    public function addMany($fields)
    {
        foreach ($fields as $field) {
            $this->add($field);
        }
        return $this;
    }

    public function readonly($condition = true)
    {
        return $condition ? $this->callFieldsMethod('readonly') : $this;
    }

    public function required($condition = true)
    {
        return $condition ? $this->callFieldsMethod('required') : $this;
    }

    private function callFieldsMethod($method, $params = [])
    {
        $this->fields()->each(function (Field $field) use ($method, $params) {
            call_user_func_array([$field, $method], $params);
        });
        return $this;
    }

    function __toString()
    {
        return view('lego::default.group', ['group' => $this])->render();
    }
}
