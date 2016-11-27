<?php namespace Lego\Field\Operators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lego\Field\Field;

/**
 * Class EloquentOperator
 * @package Lego\Field\Plugin
 */
trait EloquentOperator
{
    /**
     * Relation 字符串，eg：suite.xiaoqu.area
     * @var string
     */
    private $relation;

    /**
     * Relation 使用的字段，例如：xiaoqu_id （ suite ）
     *
     * @var string
     */
    private $foreignKey;

    /**
     * 最终关联到的 Model ，非数据实例，仅可用于 query、relation 等用途
     *
     * @var Model
     */
    private $related;

    public function relation()
    {
        return $this->relation;
    }

    public function foreignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return Model
     */
    public function related()
    {
        return $this->related;
    }

    protected function initializeEloquentOperator()
    {
        // $this->name eg: author.country.name
        $names = explode('.', $this->name());
        if (count($names) === 1) {
            return;
        }

        $this->relation = join('.', array_slice($names, 0, -1)); // eg: author.country
        $this->column = last($names); // eg: name

        // 计算 related model
        $this->related = $this->getRelated();

        // 计算 relation column
        $first = $this->getModel()->{$names[0]}();
        if ($first instanceof BelongsTo) {
            $this->foreignKey = $first->getForeignKey();
        }
    }

    protected function isNestedRelation()
    {
        return strpos($this->relation, '.') !== false;
    }

    private function getRelated(Model $model = null, $relationName = null)
    {
        $model = $model ?: $this->getModel();
        if (is_null($relationName)) {
            $copy = $model;
            foreach (explode('.', $this->relation) as $name) {
                $copy = $this->getRelated($copy, $name);
            }
            return $copy;
        }

        lego_assert(
            method_exists($model, $relationName),
            get_class($model) . " . not exists method {$relationName}()"
        );

        /** @var Relation $relation */
        $relation = $model->{$relationName}();
        lego_assert($relation instanceof Relation, "{$relationName}() result must be instance of Relation");

        return $relation->getRelated();
    }

    private function getModel(): Model
    {
        return $this->source()->original()->getModel();
    }

    /**
     * Laravel Validation unique
     *
     * Auto except current model
     *
     * https://laravel.com/docs/master/validation#rule-unique
     */
    public function unique($id = null, $idColumn = null, $extra = null)
    {
        /** @var Field $this */
        /** @var Model $model */
        $model = $this->source()->original();

        $id = $id ?: $model->getKey() ?: 'NULL';
        $idColumn = $idColumn ?: $model->getKeyName();

        $parts = [
            "unique:{$model->getConnectionName()}.{$model->getTable()}",
            $this->column(),
            $id,
            $idColumn
        ];

        if ($extra) {
            $parts [] = trim($extra, ',');
        }

        $this->rule(join(',', $parts));

        return $this;
    }
}