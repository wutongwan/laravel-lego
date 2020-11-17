<?php

namespace Lego\Set\Filter;

use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait FilterInputWrapperScope
 * @package  Lego\Set\Filter
 *
 * @template Q  Filter 构建时传入的 query 对象
 */
trait FilterInputWrapperScope
{
    /**
     * @var string|Closure(Builder|Q, scalar):Builder|Q
     */
    private $scope;

    /**
     * 自定义的过滤逻辑.
     *
     * $scope 可以为 string 或 Closure
     *  - string ：见 Eloquent Query Scopes
     *  - null ：同上 string ，自动使用当前 field name 作为 scope 函数名
     *  - Closure ：传入的 Closure 接收两个参数
     *      - $query , Filter 原始 query 对象 (eg: Laravel Eloquent Builder)
     *      - $value , 此项的输入值
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
     *  $field->scope(function (\Illuminate\Database\Eloquent\Builder $query, $value) {
     *      return $filter->where('floor', 10);
     *  });
     *
     * @param string|Closure $scope
     * @psalm-var string|Closure(Builder|Q, scalar):Builder|Q
     *
     * @return $this
     * @throws \Lego\Foundation\Exceptions\LegoException
     *
     */
    public function scope($scope = null)
    {
        lego_assert(is_null($scope) || is_string($scope) || $scope instanceof Closure, 'illegal $scope');

        $this->scope = $scope ?: $this->getInput()->getFieldName()->getOriginal();
        return $this;
    }

    public function getScope()
    {
        return $this->scope;
    }
}
