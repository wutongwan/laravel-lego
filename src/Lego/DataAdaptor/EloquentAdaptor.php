<?php

namespace Lego\DataAdaptor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;
use Lego\Foundation\FieldName;
use PhpOption\None;
use PhpOption\Option;
use function json_decode;
use function json_encode;

class EloquentAdaptor extends DataAdaptor
{
    /**
     * @var Model
     */
    protected $data;

    /**
     * 存放所有需要 save 的 model
     *
     * @var \SplObjectStorage<Model>|Model[]
     */
    private $staging;

    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->staging = new \SplObjectStorage();
    }

    public function getFieldValue(FieldName $fieldName): Option
    {
        if ($fieldName->getRelation()) {
            /** @var Model|null $related */
            $related = $this->data->getRelationValue($fieldName->getRelation());
            $value = $related ? $related->getAttribute($fieldName->getColumn()) : null;
        } else {
            $value = $this->data->getAttribute($fieldName->getColumn());
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
            $model = $this->data->getRelationValue($fieldName->getRelation())
                ?: $this->tryCreateFreshRelated($fieldName);
        } else {
            $model = $this->data;
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
        $this->staging->contains($model) || $this->staging->attach($model); // 放入待存储列表
    }

    private function tryCreateFreshRelated(FieldName $fieldName)
    {
        if ($fieldName->getRelationDepth() > 1) {
            throw new \InvalidArgumentException("Cannot create fresh relation: `{$fieldName->getRelation()}`");
        }

        /** @var Relation $relation */
        $relation = $this->data->{$fieldName->getRelation()}();
        return $relation->make();
    }

    public function save()
    {
        foreach ($this->staging as $model) {
            if ($model->save()) {
                $this->staging->detach($model);
                continue;
            }
            throw new LegoSaveModelFail($model);
        }
        return true;
    }

    /**
     * Laravel Validation unique.
     *
     * Auto except current model
     *
     * https://laravel.com/docs/master/validation#rule-unique
     */
    public function createUniqueRule()
    {
        $rule = Rule::unique($this->data->getTable());
        if ($this->data->getKey()) {
            $rule->ignore($this->data->getKey(), $this->data->getKeyName());
        }
        return $rule;
    }
}
