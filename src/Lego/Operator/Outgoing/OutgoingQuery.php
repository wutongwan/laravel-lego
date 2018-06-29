<?php

namespace Lego\Operator\Outgoing;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Lego\Foundation\Exceptions\NotSupportedException;
use Lego\Operator\SuggestResult;

class OutgoingQuery extends \Lego\Operator\Query
{
    use OutgoingParser;

    protected $wheres = [];
    protected $limit;
    protected $orders = [];
    protected $pagination = [];

    public function whereEquals($attribute, $value)
    {
        return $this->addWhere($attribute, '=', $value);
    }

    protected function addWhere($attribute, $operator, $value)
    {
        $this->wheres[] = [
            'attribute' => $attribute,
            'operator'  => $operator,
            'value'     => $value,
        ];

        return $this;
    }

    public function whereIn($attribute, array $values)
    {
        return $this->addWhere($attribute, 'in', $values);
    }

    public function whereGt($attribute, $value, bool $equals = false)
    {
        return $this->addWhere($attribute, $equals ? '>=' : '>', $value);
    }

    public function whereLt($attribute, $value, bool $equals = false)
    {
        return $this->addWhere($attribute, $equals ? '<=' : '<', $value);
    }

    public function whereContains($attribute, string $value)
    {
        return $this->addWhere($attribute, 'contains', $value);
    }

    public function whereStartsWith($attribute, string $value)
    {
        return $this->addWhere($attribute, 'contains:starts_with', $value);
    }

    public function whereEndsWith($attribute, string $value)
    {
        return $this->addWhere($attribute, 'contains:ends_with', $value);
    }

    public function whereBetween($attribute, $min, $max)
    {
        return $this->addWhere($attribute, 'between', [$min, $max]);
    }

    public function whereScope($scope, $value)
    {
        return $this->addWhere($scope, 'scope', $value);
    }

    public function suggest(
        $attribute,
        string $keyword,
        string $valueColumn = null,
        int $limit = 20
    ): SuggestResult {
        return new SuggestResult([]);
    }

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * order by.
     *
     * @param $attribute
     * @param bool $desc 默认升序(false), 如需降序, 传入 true
     *
     * @return static
     */
    public function orderBy($attribute, bool $desc = false)
    {
        $this->orders[] = [$attribute, $desc ? 'desc' : 'asc'];

        return $this;
    }

    protected function createLengthAwarePaginator($perPage, $columns, $pageName, $page)
    {
        $paginator = new LengthAwarePaginator([], 0, $perPage, $page);

        $this->pagination = [
            'perPage'     => $paginator->perPage(),
            'pageName'    => $pageName,
            'page'        => $paginator->currentPage(),
            'lengthAware' => true,
        ];

        return $paginator;
    }

    protected function createLengthNotAwarePaginator($perPage, $columns, $pageName, $page)
    {
        $paginator = new Paginator([], $perPage, $page);

        $this->pagination = [
            'perPage'     => $paginator->perPage(),
            'pageName'    => $pageName,
            'page'        => $paginator->currentPage(),
            'lengthAware' => false,
        ];

        return $paginator;
    }

    protected function select(array $columns)
    {
        throw new NotSupportedException();
    }

    public function getConditions()
    {
        return [
            'wheres'     => $this->wheres,
            'orders'     => $this->orders,
            'limit'      => $this->limit,
            'pagination' => $this->pagination,
        ];
    }

    public function toArray()
    {
        return $this->getConditions();
    }
}
