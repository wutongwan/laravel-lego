<?php namespace Lego\Widget\Grid\Concerns;

use Lego\Widget\Grid\Batch;

trait HasBatch
{
    protected $batches = [];

    public function addBatch($name, \Closure $action = null)
    {
        $action = new Batch($name, $this->query, $action);
        $this->batches[$name] = $action;
        return $action;
    }

    public function batches()
    {
        return $this->batches;
    }

    public function batch($name)
    {
        return $this->batches[$name];
    }
}
