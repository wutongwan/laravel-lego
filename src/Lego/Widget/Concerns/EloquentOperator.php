<?php namespace Lego\Widget\Concerns;

use Illuminate\Database\Eloquent\Model;

trait EloquentOperator
{
    /**
     * @return Model|null
     */
    public function model()
    {
        return $this->data();
    }
}
