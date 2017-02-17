<?php namespace Lego\Widget\Concerns;

use Illuminate\Database\Eloquent\Model;
use Lego\Data\Row\EloquentRow;

trait EloquentOperator
{
    /**
     * @return Model|null
     */
    public function model()
    {
        $data = $this->data();
        return $data instanceof EloquentRow ? $data->original() : null;
    }
}
