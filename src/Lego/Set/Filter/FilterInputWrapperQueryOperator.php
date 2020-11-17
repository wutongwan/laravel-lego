<?php

namespace Lego\Set\Filter;

use Lego\Contracts\QueryOperators;

/**
 * 筛选运算符相关代码，默认判定【等于】，其他条件可调用对应的 whereXxx 方法
 *
 * Trait FilterInputWrapperQueryOperator
 * @package Lego\Set\Filter
 */
trait FilterInputWrapperQueryOperator
{
    /**
     * @var string
     */
    private $queryOperator = QueryOperators::QUERY_EQ;

    private function setQueryOperator(string $operator)
    {
        $this->queryOperator = $operator;
        return $this;
    }

    /**
     * @return string
     */
    public function getQueryOperator(): string
    {
        return $this->queryOperator;
    }

    public function whereEquals()
    {
        return $this->setQueryOperator(QueryOperators::QUERY_EQ);
    }

    public function whereGt()
    {
        return $this->setQueryOperator(QueryOperators::QUERY_GT);
    }

    public function whereGte()
    {
        return $this->setQueryOperator(QueryOperators::QUERY_GTE);
    }

    public function whereLt()
    {
        return $this->setQueryOperator(QueryOperators::QUERY_LT);
    }

    public function whereLte()
    {
        return $this->setQueryOperator(QueryOperators::QUERY_LTE);
    }

    public function whereContains()
    {
        return $this->setQueryOperator(QueryOperators::QUERY_CONTAINS);
    }

    public function whereStartsWith()
    {
        return $this->setQueryOperator(QueryOperators::QUERY_STARTS_WITH);
    }

    public function whereEndsWith()
    {
        return $this->setQueryOperator(QueryOperators::QUERY_ENDS_WITH);
    }
}
