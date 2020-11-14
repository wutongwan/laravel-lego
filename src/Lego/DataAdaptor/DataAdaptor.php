<?php

namespace Lego\DataAdaptor;

use Lego\Foundation\Exceptions\LegoException;
use Lego\Foundation\FieldName;
use Lego\Foundation\Match\MatchQuery;
use Lego\Foundation\Match\MatchResults;
use PhpOption\Option;

abstract class DataAdaptor
{
    protected $original;

    public function __construct($data)
    {
        $this->original = $data;
    }

    public function getOriginal()
    {
        return $this->original;
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

    abstract public function save();

    abstract public function queryMatch(FieldName $fieldName, MatchQuery $match): MatchResults;

    public function createUniqueRule()
    {
        throw new LegoException(
            'Validation: `unique` rule only worked for Eloquent, ' .
            'you can use `validator($closure)` implement unique validation.'
        );
    }
}
