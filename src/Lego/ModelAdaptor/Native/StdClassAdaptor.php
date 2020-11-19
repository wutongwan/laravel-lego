<?php

namespace Lego\ModelAdaptor\Native;

use Lego\Foundation\Exceptions\NotSupportedException;
use Lego\Foundation\FieldName;
use Lego\Foundation\Match\MatchQuery;
use Lego\Foundation\Match\MatchResults;
use Lego\ModelAdaptor\ModelAdaptor;
use Lego\Utility\JsonUtility;
use PhpOption\Option;

class StdClassAdaptor extends ModelAdaptor
{
    public function getKeyName(FieldName $fieldName): string
    {
        throw new NotSupportedException(__METHOD__);
    }

    public function getFieldValue(FieldName $fieldName): Option
    {
        $value = $this->get($this->model, $fieldName->getQualifiedColumnName());
        if ($fieldName->getJsonPath()) {
            $value = JsonUtility::get($value, $fieldName->getJsonPath());
        }
        return Option::fromValue($value);
    }

    public function setFieldValue(FieldName $fieldName, $value): void
    {
        if ($fieldName->getJsonPath()) {
            $columnValue = $this->get($this->model, $fieldName->getQualifiedColumnName());
            $columnValue = JsonUtility::set($columnValue, $fieldName->getJsonPath(), $value);
            $this->set($this->model, $fieldName->getQualifiedColumnName(), $columnValue);
            return;
        }

        $this->set($this->model, $fieldName->getQualifiedColumnName(), $value);
    }

    protected function get($data, $key)
    {
        return data_get($data, $key);
    }

    protected function set(&$data, $key, $value): void
    {
        data_set($data, $key, $value);
    }

    public function setRelated(FieldName $fieldName, $related): void
    {
        throw new NotSupportedException(__METHOD__);
    }

    public function unsetRelated(FieldName $fieldName, $related = null): void
    {
        throw new NotSupportedException(__METHOD__);
    }

    public function save()
    {
    }

    public function queryMatch(FieldName $fieldName, MatchQuery $query): MatchResults
    {
        return new MatchResults();
    }
}
