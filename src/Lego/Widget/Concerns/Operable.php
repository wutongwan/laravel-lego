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
    public $store;

    final protected function initializeOperator($data)
    {
        $this->data = $data;
        $this->query = Finder::query($data);
        $this->store = Finder::store($data);
    }
}
