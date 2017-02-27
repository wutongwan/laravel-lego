<?php namespace Lego\Widget\Concerns;

use Lego\Operator\Finder;
use Lego\Operator\Query\Query;
use Lego\Operator\Store\Store;

/**
 * Operator 的承载类，用于拆分代码
 */
trait Operable
{
    protected $data;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var Store
     */
    protected $store;

    final protected function initializeDataOperator($data)
    {
        if ($data instanceof Store) {
            $this->data = $data->getOriginalData();
            $this->store = $data;
            $this->query = Finder::query($this->data);
        } elseif ($data instanceof Query) {
            $this->data = $data->getOriginalData();
            $this->store = Finder::store($this->data);
            $this->query = $data;
        } else {
            $this->data = $data;
            $this->query = Finder::query($data);
            $this->store = Finder::store($data);
        }
    }

    public function getStore()
    {
        return $this->store;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
