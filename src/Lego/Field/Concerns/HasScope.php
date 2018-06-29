<?php

namespace Lego\Field\Concerns;

use Lego\Operator\Query;

/**
 * Class HasScope.
 */
trait HasScope
{
    protected $scope;

    /**
     * 自定义的过滤逻辑.
     *
     * $scope 可以为 string 或 Closure
     *  - string ：见 Eloquent Query Scopes
     *  - null ：同上 string ，自动使用当前 field name 作为 scope 函数名
     *  - Closure ：传入的 Closure 接收两个参数
     *      - $query , instanceof \Lego\Operator\Query ，注意：此处不是 Laravel 中的 Query Builder
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
     *
     * @throws \Lego\Foundation\Exceptions\LegoException
     *
     * @return $this
     */
    public function scope($scope = null)
    {
        lego_assert(is_null($scope) || is_string($scope) || $scope instanceof \Closure, 'illegal $scope');

        $this->scope = $scope ?: $this->name();

        return $this;
    }

    public function callScope(Query $query)
    {
        if (is_string($this->scope)) {
            $query->whereScope($this->scope, $this->getNewValue());
        } else {
            call_user_func_array($this->scope, [$query, $this->getNewValue()]);
        }

        return $query;
    }
}
