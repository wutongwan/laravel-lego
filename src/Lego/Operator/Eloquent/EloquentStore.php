<?php

namespace Lego\Operator\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Lego\Field\FieldNameSlicer;
use Lego\Foundation\Exceptions\LegoSaveFail;
use Lego\Operator\Finder;
use Lego\Operator\Store;

/**
 * Class EloquentStore.
 *
 * @property Model $data
 */
class EloquentStore extends Store
{
    use HasRelation;

    public static function parse($data)
    {
        if ($data instanceof Model) {
            return new self($data);
        }

        if (is_string($data) && is_subclass_of($data, Model::class)) {
            return new self(new $data());
        }

        return false;
    }

    protected $relations = [];

    public function getKeyName()
    {
        return $this->data->getKeyName();
    }

    /**
     * 获取属性值
     *
     * @param $attribute
     * @param null $default
     *
     * @return mixed
     */
    public function get($attribute, $default = null)
    {
        list($relationArray, $column, $jsonArray) = FieldNameSlicer::split($attribute);
        $key = join('.', array_merge($relationArray, [$column]));
        $value = data_get($this->data, $key);

        if ($jsonArray) {
            $value = is_string($value) ? json_decode($value) : $value;

            return data_get($value, join('.', $jsonArray), $default);
        }

        return is_null($value) ? $default : $value;
    }

    /**
     * 修改属性值
     *
     * @param $attribute
     * @param $value
     */
    public function set($attribute, $value)
    {
        list($relationArray, $column, $jsonArray) = FieldNameSlicer::split($attribute);
        $column = $jsonArray ? ($column . '->' . join('->', $jsonArray)) : $column;

        if (count($relationArray) === 0) {
            $this->data->setAttribute($column, $value);

            return;
        }

        $relation = join('.', $relationArray);
        $related = $this->data->getRelationValue($relation);
        if ($related && $related instanceof Model) {
            $related->setAttribute($column, $value);
            $this->relations[$relation] = $related;
        }
    }

    /**
     * 存储操作.
     *
     * 存储时尝试先存储 Relation ，再存储 Model ，任一失败则回滚
     *
     * @param array $options
     *
     * @return bool
     * @throws LegoSaveFail
     *
     */
    public function save($options = [])
    {
        foreach ($this->relations as $related) {
            if (!$related->save()) {
                $this->throwSaveError($related);
            }
        }

        if (!$this->data->save($options)) {
            $this->throwSaveError($this->data);
        }

        $this->data = $this->data->fresh();

        return true;
    }

    private function throwSaveError($data)
    {
        $class = class_basename($data);

        try {
            $dataString = json_encode($data, JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            $dataString = '[json encode fail]';
        }

        throw new LegoSaveFail($class . ' save fail, ' . $dataString);
    }

    public function toArray()
    {
        return $this->data->toArray();
    }

    public function associate($attribute, $id)
    {
        /** @var BelongsTo $relation */
        $relation = $this->getRelationOfAttribute($attribute);
        $this->data->setAttribute($relation->getForeignKeyName(), $id);
    }

    public function dissociate($attribute)
    {
        /** @var BelongsTo $relation */
        $relation = $this->getRelationOfAttribute($attribute);
        $this->data->setAttribute($relation->getForeignKeyName(), null);
    }

    public function attach($attribute, array $ids, array $attributes = [])
    {
        $this->getRelationOfAttribute($attribute)->attach($ids, $attributes);
    }

    public function detach($attribute, array $ids)
    {
        $this->getRelationOfAttribute($attribute)->detach($ids);
    }

    protected function getRelationOfAttribute($attribute)
    {
        list($relationArray) = FieldNameSlicer::split($attribute);

        return $this->getNestedRelation($this->getModel(), $relationArray);
    }

    /**
     * 当前关联数据.
     */
    public function getAssociated($attribute)
    {
        $model = $this->getRelationValue($attribute);

        return $model ? Finder::createStore($model) : null;
    }

    /**
     * 当前关联数据.
     *
     * @param $attribute
     *
     * @return Collection|Store[]
     */
    public function getAttached($attribute): Collection
    {
        /** @var Collection $coll */
        $coll = $this->getRelationValue($attribute);

        return $coll->map(function ($data) {
            return Finder::createStore($data);
        });
    }

    protected function getRelationValue($attribute)
    {
        list($relationArray) = FieldNameSlicer::split($attribute);

        return data_get($this->data, join('.', $relationArray));
    }
}
