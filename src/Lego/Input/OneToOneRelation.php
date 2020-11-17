<?php

namespace Lego\Input;

use Lego\Foundation\FieldName;

class OneToOneRelation extends AutoComplete
{
    /**
     * @var FieldName
     */
    private $valueFieldName;

    public function formInputHandler()
    {
        return Form\OneToOneRelationHandler::class;
    }

    /**
     * 修改关系自动补全时的值字段，默认使用 `$model->getKeyName()`
     *
     * @param string $column
     * @return $this
     */
    public function valueColumn(string $column)
    {
        $this->valueFieldName = $this->getFieldName()->cloneWith($column);
        return $this;
    }

    /**
     * @return FieldName
     */
    public function getValueFieldName(): FieldName
    {
        return $this->valueFieldName;
    }

    /**
     * @param FieldName $valueFieldName
     */
    public function setValueFieldName(FieldName $valueFieldName): void
    {
        $this->valueFieldName = $valueFieldName;
    }
}
