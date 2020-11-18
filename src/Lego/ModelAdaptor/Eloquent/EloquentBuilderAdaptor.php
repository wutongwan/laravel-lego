<?php

namespace Lego\ModelAdaptor\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Lego\Contracts\QueryOperators;
use Lego\Foundation\FieldName;
use Lego\ModelAdaptor\QueryAdaptor;

class EloquentBuilderAdaptor extends QueryAdaptor
{
    /**
     * @var Builder
     */
    protected $query;

    public function whereScope(string $scope, $value)
    {
        $this->query->{'scope' . ucfirst($scope)}($value);
    }

    private function addWhere(FieldName $name, $operator, $value)
    {
        $column = $name->getColumn();
        if ($name->getJsonPath()) {
            $column .= ('->' . join('->', $name->getJsonPathList()));
        }

        // simple where
        if (!$name->getRelation()) {
            $this->addWhereColumn($this->query, $column, $operator, $value);
            return $this;
        }

        // whereHas
        $this->query->whereHas($name->getRelation(), function ($query) use ($column, $operator, $value) {
            return $this->addWhereColumn($query, $column, $operator, $value);
        });
        return $this;
    }

    /**
     * @param Builder $query
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return Builder
     */
    private function addWhereColumn($query, $column, $operator, $value)
    {
        switch ($operator) {
            default:
                return $query->where($column, $operator, $value);

            case QueryOperators::IN:
                return $query->whereIn($column, $value);

            case QueryOperators::BETWEEN:
                return $query->whereBetween($column, $value);

            case QueryOperators::JSON_CONTAINS:
                return $query->whereJsonContains($column, $value);
        }
    }

    public function whereEquals(FieldName $name, $value)
    {
        $this->addWhere($name, '=', $value);
    }

    public function whereContains(FieldName $name, $value)
    {
        return $this->addWhere($name, 'like', '%' . trim($value) . '%');
    }

    public function whereStartsWith(FieldName $name, $value)
    {
        return $this->addWhere($name, 'like', trim($value) . '%');
    }

    public function whereEndsWith(FieldName $name, $value)
    {
        return $this->addWhere($name, 'like', '%' . trim($value));
    }

    public function whereGt(FieldName $name, $value, bool $equals = false)
    {
        $this->addWhere($name, $equals ? '>=' : '>', $value);
    }

    public function whereLt(FieldName $name, $value, bool $equals = false)
    {
        $this->addWhere($name, $equals ? '<=' : '<', $value);
    }

    public function whereIn(FieldName $name, array $values)
    {
        $this->addWhere($name, QueryOperators::IN, $values);
    }

    public function whereBetween(FieldName $name, $min, $max)
    {
        $this->addWhere($name, QueryOperators::BETWEEN, [$min, $max]);
    }

    protected function whereJsonContains(FieldName $name, $value)
    {
        $this->addWhere($name, QueryOperators::JSON_CONTAINS, $value);
    }

    public function limit(int $limit)
    {
        $this->query->limit($limit);
    }

    public function orderBy(string $column, bool $desc = false)
    {
        $this->query->orderBy($column, $desc ? 'desc' : 'asc');
    }

    public function getPaginator(int $perPage, int $page)
    {
        return $this->query->simplePaginate($perPage, ['*'], 'page', $page);
    }

    public function getLengthAwarePaginator(int $perPage, int $page)
    {
        return $this->query->paginate($perPage, ['*'], 'page', $page);
    }
}
