<?php

namespace Lego\Input;

use Lego\Foundation\FieldName;
use Lego\Foundation\Response\ResponseManager;

class OneToOneRelation extends AutoComplete
{
    /**
     * @var FieldName
     */
    private $valueFieldName;

    public function __construct(ResponseManager $responseManager)
    {
        parent::__construct($responseManager);

        $this->valueFieldName = $this->getFieldName()->cloneWith(
            $this->getAdaptor()->getKeyName($this->getFieldName())
        );
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
}
