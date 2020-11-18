<?php

namespace Lego\ModelAdaptor;

use Illuminate\Database\Eloquent\Builder;
use Lego\Contracts\QueryOperators;
use Lego\Foundation\FieldName;

abstract class QueryAdaptor
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    final public function getQuery()
    {
        return $this->query;
    }

    /**
     * Laravel scope query
     */
    abstract public function whereScope(string $scope, $value);

    public function where(FieldName $name, string $operator, $value)
    {
        switch ($operator) {
            default:
                throw new \InvalidArgumentException('Unsupported operator: ' . $operator);

            case QueryOperators::QUERY_EQ:
                $this->whereEquals($name, $value);
                break;

            case QueryOperators::QUERY_GT:
                $this->whereGt($name, $value);
                break;
            case QueryOperators::QUERY_GTE:
                $this->whereGt($name, $value, true);
                break;

            case QueryOperators::QUERY_LT:
                $this->whereLt($name, $value);
                break;
            case QueryOperators::QUERY_LTE:
                $this->whereLt($name, $value, true);
                break;

            case QueryOperators::QUERY_CONTAINS:
                $this->whereContains($name, $value);
                break;
            case QueryOperators::QUERY_STARTS_WITH:
                $this->whereStartsWith($name, $value);
                break;
            case QueryOperators::QUERY_ENDS_WITH:
                $this->whereEndsWith($name, $value);
                break;

            case QueryOperators::IN:
                $this->whereIn($name, $value);
                break;

            case QueryOperators::JSON_CONTAINS:
                $this->whereJsonContains($name, $value);
                break;

            case QueryOperators::BETWEEN:
                $this->whereBetween($name, $value[0], $value[1]);
                break;
        }
    }

    abstract protected function whereEquals(FieldName $name, $value);

    abstract protected function whereContains(FieldName $name, $value);

    abstract protected function whereStartsWith(FieldName $name, $value);

    abstract protected function whereEndsWith(FieldName $name, $value);

    abstract protected function whereGt(FieldName $name, $value, bool $equals = false);

    abstract protected function whereLt(FieldName $name, $value, bool $equals = false);

    abstract protected function whereIn(FieldName $name, array $values);

    abstract protected function whereJsonContains(FieldName $name, $value);

    abstract protected function whereBetween(FieldName $name, $min, $max);

    abstract public function limit(int $limit);

    abstract public function orderBy(string $column, bool $desc = false);

    abstract public function getLengthAwarePaginator(int $perPage, int $page);

    abstract public function getPaginator(int $perPage, int $page);
}
