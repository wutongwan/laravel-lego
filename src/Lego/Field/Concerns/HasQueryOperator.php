<?php namespace Lego\Field\Concerns;

use Lego\Foundation\Exceptions\LegoException;
use Lego\Operator\Query;

trait HasQueryOperator
{
    protected $queryOperator = self::QUERY_EQ;

    public function filter(Query $query)
    {
        if (!$this->queryOperator) {
            return $query;
        }

        $mapping = [
            self::QUERY_EQ => 'whereEquals',
            self::QUERY_GT => 'whereGt',
            self::QUERY_GTE => 'whereGte',
            self::QUERY_LT => 'whereLt',
            self::QUERY_LTE => 'whereLte',
            self::QUERY_STARTS_WITH => 'whereStartsWith',
            self::QUERY_ENDS_WITH => 'whereEndsWith',
            self::QUERY_CONTAINS => 'whereContains',
        ];

        $method = $mapping[$this->queryOperator] ?? null;
        if (is_null($method)) {
            throw new LegoException("Unexpected query operator: {$this->queryOperator}");
        }

        return call_user_func_array([$query, $method], [$this->name(), $this->getNewValue()]);
    }

    /**
     * 设定查询时的操作符.
     *
     * @param string $operator 参照 QUERY_* 常量
     *
     * @return $this
     */
    public function setQueryOperator($operator)
    {
        $this->queryOperator = $operator;

        return $this;
    }

    public function whereEquals()
    {
        return $this->setQueryOperator(self::QUERY_EQ);
    }

    public function whereGt()
    {
        return $this->setQueryOperator(self::QUERY_GT);
    }

    public function whereGte()
    {
        return $this->setQueryOperator(self::QUERY_GTE);
    }

    public function whereLt()
    {
        return $this->setQueryOperator(self::QUERY_LT);
    }

    public function whereLte()
    {
        return $this->setQueryOperator(self::QUERY_LTE);
    }

    public function whereContains()
    {
        return $this->setQueryOperator(self::QUERY_CONTAINS);
    }

    public function whereStartsWith()
    {
        return $this->setQueryOperator(self::QUERY_STARTS_WITH);
    }

    public function whereEndsWith()
    {
        return $this->setQueryOperator(self::QUERY_ENDS_WITH);
    }

}
