<?php

namespace Lego\ModelAdaptor\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Lego\Foundation\FieldName;
use Lego\Foundation\Match\MatchQuery;
use Lego\Foundation\Match\MatchResults;
use Lego\ModelAdaptor\LegoSaveModelFail;
use Lego\ModelAdaptor\ModelAdaptor;
use Lego\Utility\EloquentUtility;
use Lego\Utility\JsonUtility;
use PhpOption\None;
use PhpOption\Option;
use SplObjectStorage;

class EloquentAdaptor extends ModelAdaptor
{
    /**
     * @var Model
     */
    protected $model;

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
            return $this->model->getKeyName();
        }
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
            $value = JsonUtility::get($value, $fieldName->getJsonPath());
        }

        return Option::fromValue($value);
    }

    public function setFieldValue(FieldName $fieldName, $value): void
    {
        if ($fieldName->getRelation()) {
            /** @var Model|null $relation */
            $model = $this->getRelated($fieldName) ?: $this->tryCreateFreshRelated($fieldName);
        } else {
            $model = $this->model;
        }

        if ($fieldName->getJsonPath()) {
            $columnValue = $model->getAttribute($fieldName->getColumn());
            $columnValue = JsonUtility::set($columnValue, $fieldName->getJsonPath(), $value);
        } else {
            $columnValue = $value;
        }

        $model->setAttribute($fieldName->getColumn(), $columnValue);
        $this->addStaging($model); // 放入待存储列表
    }

    public function getRelated(FieldName $fieldName)
    {
        $related = $this->model;
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
        $related = $this->model;
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
            get_class($this->model)
        ));
    }

    private function tryCreateFreshRelated(FieldName $fieldName)
    {
        if ($fieldName->getRelationDepth() > 1) {
            throw new InvalidArgumentException("Cannot create fresh relation: `{$fieldName->getRelation()}`");
        }

        /** @var Relation $relation */
        $relation = $this->model->{$fieldName->getRelation()}();
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
        $this->addStaging($this->model);

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
        $rule = Rule::unique($this->model->getTable());
        if ($this->model->getKey()) {
            $rule->ignore($this->model->getKey(), $this->model->getKeyName());
        }
        return $rule;
    }

    public function queryMatch(FieldName $fieldName, MatchQuery $query): MatchResults
    {
        if ($fieldName->getRelation()) {
            $model = $this->getRelation($fieldName)->getRelated();
            $valueColumn = $model->getKeyName();
        } else {
            $model = $this->model;
            $valueColumn = $fieldName->getColumn();
        }

        return EloquentUtility::match(
            $model->newQuery(),
            $query,
            $fieldName->getColumn(),
            $valueColumn
        );
    }

    public function setRelated(FieldName $fieldName, $related): void
    {
        list($model, $relation) = $this->getTargetRelation($fieldName);

        switch (true) {
            default:
                throw new InvalidArgumentException("Unsupported relation type: " . get_class($relation));

            case $relation instanceof BelongsTo:
                $relation->associate($related);
                break;

            case $relation instanceof HasOne:
                $related->{$relation->getForeignKeyName()} = $model->{$relation->getLocalKeyName()};
                $this->addStaging($related);
                break;

            case $relation instanceof BelongsToMany:
                $relation->attach($related);
                break;
        }
    }

    public function unsetRelated(FieldName $fieldName, $related = null): void
    {
        list($_, $relation) = $this->getTargetRelation($fieldName);

        switch (true) {
            default:
                throw new InvalidArgumentException("Unsupported relation type: " . get_class($relation));

            case $relation instanceof BelongsTo:
                $relation->dissociate();
                break;

            case $relation instanceof HasOne:
                $related->{$relation->getForeignKeyName()} = null;
                $this->addStaging($related);
                break;

            case $relation instanceof BelongsToMany:
                $relation->detach($related);
                break;
        }
    }

    private function getTargetRelation(FieldName $fieldName)
    {
        if (empty($relations = $fieldName->getRelationList())) {
            throw new InvalidArgumentException("Invalid field name {$fieldName}");
        }

        $target = array_pop($relations);
        // 获取到倒数第二个关系值
        $model = $this->model;
        foreach ($relations as $relation) {
            $model = $model->getRelationValue($relation);
            if (!$model) {
                throw new InvalidArgumentException(sprintf("Relationship value not exists: %s", join($relations)));
            }
        }

        return [
            $model,
            $model->{$target}()
        ];
    }
}
