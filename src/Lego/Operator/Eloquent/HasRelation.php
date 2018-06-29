<?php

namespace Lego\Operator\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasRelation
{
    /**
     * @param $relationArray
     *
     * @return
     */
    protected function getNestedRelation(Model $model, $relationArray)
    {
        $relation = null;
        foreach ($relationArray as $relation) {
            /** @var Relation $relation */
            $relation = $model->newQuery()->getRelation($relation);
            if ($relation) {
                $model = $relation->getRelated();
            }
        }

        return $relation;
    }

    protected function getModel()
    {
        if ($this->data instanceof Model) {
            return $this->data;
        }

        if ($this->data instanceof EloquentQueryBuilder) {
            return $this->data->getModel();
        }

        return null;
    }
}
