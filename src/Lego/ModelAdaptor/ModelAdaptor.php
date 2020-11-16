<?php

namespace Lego\ModelAdaptor;

use Lego\Foundation\Exceptions\LegoException;
use Lego\Foundation\FieldName;
use Lego\Foundation\Match\MatchQuery;
use Lego\Foundation\Match\MatchResults;
use PhpOption\Option;

abstract class ModelAdaptor
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    final public function getModel()
    {
        return $this->model;
    }

    /**
     * 获取 field 所属 表/对象 主键/索引 的字段名/属性名
     *
     * @param FieldName $fieldName
     * @return string
     */
    abstract public function getKeyName(FieldName $fieldName): string;

    abstract public function getFieldValue(FieldName $fieldName): Option;

    abstract public function setFieldValue(FieldName $fieldName, $value): void;

    abstract public function setRelated(FieldName $fieldName, $related): void;

    abstract public function unsetRelated(FieldName $fieldName, $related = null): void;

    abstract public function save();

    abstract public function queryMatch(FieldName $fieldName, MatchQuery $query): MatchResults;

    public function createUniqueRule()
    {
        throw new LegoException(
            'Validation: `unique` rule only worked for Eloquent, ' .
            'you can use `validator($closure)` implement unique validation.'
        );
    }
}
