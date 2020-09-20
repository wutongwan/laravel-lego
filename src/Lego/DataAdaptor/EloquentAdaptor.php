<?php

namespace Lego\DataAdaptor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lego\Foundation\FieldName;
use PhpOption\None;
use PhpOption\Option;
use function json_decode;
use function json_encode;

class EloquentAdaptor
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var \SplObjectStorage
     */
    private $waitingSave;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->waitingSave = new \SplObjectStorage();
    }

    public function getFieldValue(FieldName $fieldName): Option
    {
        if ($fieldName->getRelation()) {
            /** @var Model|null $related */
            $related = $this->model->getRelationValue($fieldName->getRelation());
            $value = $related ? $related->getAttribute($fieldName->getColumn()) : null;
        } else {
            $value = $this->model->getAttribute($fieldName->getColumn());
        }

        if ($value === null) {
            return None::create();
        }

        if ($fieldName->getJsonPath()) {
            $value = data_get(is_string($value) ? json_decode($value) : $value, $fieldName->getJsonPath());
        }

        return Option::fromValue($value);
    }

    public function setFieldValue(FieldName $fieldName, $value): void
    {
        if ($fieldName->getRelation()) {
            /** @var Model|null $relation */
            $model = $this->model->getRelationValue($fieldName->getRelation())
                ?: $this->tryCreateFreshRelated($fieldName);
        } else {
            $model = $this->model;
        }

        if ($fieldName->getJsonPath()) {
            $columnValue = $model->getAttribute($fieldName->getColumn());
            $columnValue = ($isString = is_string($columnValue)) ? json_decode($columnValue) : $columnValue;
            data_set($columnValue, $fieldName->getJsonPath(), $value);
            $isString && ($columnValue = json_encode($columnValue)); // 保证类型一致
        } else {
            $columnValue = $value;
        }

        $model->setAttribute($fieldName->getColumn(), $columnValue);
        $this->waitingSave->contains($model) || $this->waitingSave->attach($model); // 放入待存储列表
    }

    private function tryCreateFreshRelated(FieldName $fieldName)
    {
        if ($fieldName->getRelationDepth() > 1) {
            throw new \InvalidArgumentException("Cannot create fresh relation: `{$fieldName->getRelation()}`");
        }

        /** @var Relation $relation */
        $relation = $this->model->{$fieldName->getRelation()}();
        return $relation->make();
    }
}
