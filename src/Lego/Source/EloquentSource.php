<?php namespace Lego\Source;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Laravel ORM Source
 *
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 * @mixin Collection
 */
class EloquentSource extends Source
{
    /** @var Eloquent[]|Collection|QueryBuilder|EloquentBuilder $data */
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    function __call($name, $arguments)
    {
        return call_user_func_array([$this->data, $name], $arguments);
    }
}