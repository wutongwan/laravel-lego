<?php namespace Lego\Field\Plugin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lego\Field\Field;

/**
 * Class EloquentPlugin
 * @package Lego\Field\Plugin
 */
trait EloquentPlugin
{
    /**
     * Relation 字符串，eg：suite.xiaoqu.area
     * @var string
     */
    private $relation;

    /**
     * Relation 数组，eg: ['suite', 'xiaoqu', 'area']
     * @var array
     */
    private $relationArray;

    /**
     * @var Model
     */
    private $related;

    public function relation()
    {
        return $this->relation;
    }

    protected function relationAsArray()
    {
        return $this->relationArray;
    }

    protected function initializeEloquentPlugin()
    {
        $names = explode('.', $this->name());
        if (count($names) === 1) {
            return;
        }

        $this->column = last($names);
        $this->relationArray = array_slice($names, 0, -1);
        $this->relation = join('.', $this->relationArray);

        $this->related = $this->calculateRelated();
    }

    private function calculateRelated(Model $model = null, $relationName = null)
    {
        if (is_null($model) && is_null($relationName)) {
            $model = $this->source()->original()->getModel();
            foreach ($this->relationAsArray() as $name) {
                $model = $this->calculateRelated($model, $name);
            }
            return $model;
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

    /**
     * @return Model
     */
    protected function getRelated()
    {
        return $this->related;
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