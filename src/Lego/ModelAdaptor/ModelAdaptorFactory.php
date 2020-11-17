<?php

namespace Lego\ModelAdaptor;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ModelAdaptorFactory
{
    public function makeModel($model): ModelAdaptor
    {
        if ($model instanceof Model) {
            return new Eloquent\EloquentAdaptor($model);
        }

        throw new InvalidArgumentException('Unsupported $model type');
    }

    public function makeQuery($query)
    {
        switch (true) {
            case $query instanceof EloquentBuilder:
                return new Eloquent\EloquentBuilderAdaptor($query);
        }

        throw new InvalidArgumentException('Unsupported $query type');
    }
}
