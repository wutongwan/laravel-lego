<?php

namespace Lego\Field\Concerns;

use Lego\Field\Condition;
use Lego\Field\Field;
use Lego\Foundation\Facades\LegoAssets;

trait ConditionOfGroup
{
    protected $condition;

    public function condition($field, $operator, $expected)
    {
        $field = $field instanceof Field ? $field : $this->fields[$field];
        $this->condition = new Condition($field, $operator, $expected);

        LegoAssets::js('field/condition-group.js');

        return $this;
    }

    /**
     * @return Condition
     */
    public function getCondition()
    {
        return $this->condition;
    }
}
