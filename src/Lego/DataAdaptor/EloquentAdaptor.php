<?php

namespace Lego\DataAdaptor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;
use Lego\Foundation\FieldName;
use Lego\Foundation\Match\MatchQuery;
use Lego\Foundation\Match\MatchResults;
use Lego\Utility\EloquentUtility;
use PhpOption\None;
use PhpOption\Option;
use SplObjectStorage;
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
     * @var SplObjectStorage<Model>|Model[]
     */
    private $staging;

    public function __construct(Model $model)
    {
        parent::__construct($model);

        $this->staging = new SplObjectStorage();
    }

    // 获取 FieldName 对应表的主键值
    public function getKeyName(FieldName $fieldName): string
    {
        if ($fieldName->getRelation()) {
            return $this->getRelation($fieldName)->getRelated()->getKeyName();
        } else {
            return $this->data->getKeyName();
        }
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
            $model = $this->getRelated($fieldName) ?: $this->tryCreateFreshRelated($fieldName);
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
        $this->addStaging($model); // 放入待存储列表
    }

    private function getRelated(FieldName $fieldName)
    {
        $related = $this->data;
        foreach ($fieldName->getRelationList() as $name) {
            if (!$related = $related->{$name}) {
                return null;
            }
        }
        return $related;
    }

    // 获取 Relation 对象，支持多层 Relation
    private function getRelation(FieldName $fieldName): Relation
    {
        $related = $this->data;
        $relationList = $fieldName->getRelationList();
        while ($name = array_shift($relationList)) {
            $relation = $related->{$name}();
            if (!$relation instanceof Relation) {
                throw new \LogicException(sprintf(
                    '%s::%s must return a relationship instance.',
                    get_class($related),
                    $name
                ));
            }
            if (count($relationList) > 0) {
                $related = $relation->getRelated();
                continue;
            }
            return $relation;
        }

        throw new \LogicException(sprintf(
            'Relation [%s] not exists in model[%s]',
            $fieldName->getRelation(),
            get_class($this->data)
        ));
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

    // 暂存 model ，在 save 中一起保存
    private function addStaging(Model $model)
    {
        $this->staging->contains($model) || $this->staging->attach($model);
    }

    public function save()
    {
        /// 不管怎样，都要调用一次当前 model 的 save，
        /// 因为有可能 form field 的 mutator 对 model 进行了修改
        /// model->save() 中会进行 isDirty 判定，不会产生无意义写库
        $this->addStaging($this->data);

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

    public function queryMatch(FieldName $fieldName, MatchQuery $match): MatchResults
    {
        if ($fieldName->getRelation()) {
            $model = $this->getRelation($fieldName)->getRelated();
            $valueColumn = $model->getKeyName();
        } else {
            $model = $this->data;
            $valueColumn = $fieldName->getColumn();
        }

        return EloquentUtility::match(
            $model->newQuery(),
            $match,
            $fieldName->getColumn(),
            $valueColumn
        );
    }
}
