<?php namespace Lego\Field\Concerns;

use Lego\Operator\Query\Query;

/**
 * Class ScopeOperator
 * @package Lego\Field\Operators
 */
trait ScopeOperator
{
    private $scope;

    /**
     * 自定义的过滤逻辑
     *
     * $scope 可以为 string 或 Closure
     *  - string ：见 Eloquent Query Scopes
     *  - Closure ：传入的 Closure 接收两个参数
     *      - $query , instanceof \Lego\Operator\Query\Query ，注意：此处不是 Laravel 中的 Query Builder
     *      - $value , 此项的输入值
     *
     *
     * Example:
     *
     * $field = $filter->addSelect('floor', 'Floor')->values(['low', 'high']);
     *
     * - $scope is string
     *  $field->scope('height');
     *      => call model method `scopeHeight($value)`
     *      => $value could be `low` or `high`
     *
     * - $scope is Closure
     *  $field->scope(function (Query $filter, $value) {
     *      if ($value == 'low') {
     *          return $filter->whereLte('floor', 10);
     *      } else {
     *          return $filter->whereGt('floor', 10);
     *      }
     *  });
     *
     * @param string|\Closure $scope
     * @return $this
     */
    public function scope($scope)
    {
        lego_assert(is_string($scope) || $scope instanceof \Closure, 'illegal $scope');

        $this->scope = $scope;

        return $this;
    }

    public function callScope(Query $query)
    {
        if (is_string($this->scope)) {
            $query->{$this->scope}($this->getNewValue());
        } else {
            call_user_func_array($this->scope, [$query, $this->getNewValue()]);
        }

        return $query;
    }
}
