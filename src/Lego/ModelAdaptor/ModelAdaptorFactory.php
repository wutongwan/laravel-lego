<?php

namespace Lego\ModelAdaptor;

use ArrayAccess;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Lego\Contracts\HasQueryAdaptor;

class ModelAdaptorFactory
{
    public function makeModel($model): ModelAdaptor
    {
        switch (true) {
            default:
                throw new InvalidArgumentException('Unsupported $model type');

            case $model instanceof Model:
                return new Eloquent\EloquentAdaptor($model);

            case is_array($model) || $model instanceof ArrayAccess:
                return new Native\ArrayAdaptor($model);

            case is_object($model):
                return new Native\StdClassAdaptor($model);
        }
    }

    public function makeQuery($query)
    {
        switch (true) {
            default:
                throw new InvalidArgumentException('Unsupported $query type');

            case $query instanceof EloquentBuilder:
                return new Eloquent\EloquentBuilderAdaptor($query);

            case $query instanceof HasQueryAdaptor:
                return $query->getQueryAdaptor();

            case $query instanceof QueryAdaptor:
                return $query;
        }
    }
}
