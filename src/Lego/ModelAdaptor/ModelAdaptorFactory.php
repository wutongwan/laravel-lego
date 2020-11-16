<?php

namespace Lego\ModelAdaptor;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ModelAdaptorFactory
{
    public function make($model): ModelAdaptor
    {
        if ($model instanceof Model) {
            return new EloquentAdaptor($model);
        }

        throw new InvalidArgumentException('Unsupported $model type');
    }
}
