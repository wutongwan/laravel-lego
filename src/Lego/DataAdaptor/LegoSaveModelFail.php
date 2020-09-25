<?php

namespace Lego\DataAdaptor;

use Illuminate\Database\Eloquent\Model;
use Lego\Foundation\Exceptions\LegoSaveFail;

class LegoSaveModelFail extends LegoSaveFail
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;

        parent::__construct(
            sprintf('Model[%s] save fail', get_class($model))
        );
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
