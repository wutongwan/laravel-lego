<?php namespace Lego\Operator\Plastic;

use Illuminate\Database\Eloquent\Model;
use Sleimanx2\Plastic\Fillers\FillerInterface;
use Sleimanx2\Plastic\PlasticResult as Result;

class PlasticEloquentFiller implements FillerInterface
{
    protected $columns;
    protected $with;

    public function select(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function with(array $relations)
    {
        $this->with = $relations;
        return $this;
    }

    public function fill(Model $model, Result $result)
    {
        $ids = $result->hits()->pluck('_id')->toArray();

        $models = $model->with($this->with)->findMany($ids, $this->columns);

        $result->setHits($models);
    }
}
