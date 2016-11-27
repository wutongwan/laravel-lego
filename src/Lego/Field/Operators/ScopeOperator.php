<?php namespace Lego\Field\Operators;

use Lego\Data\Table\Table;

/**
 * Class ScopeOperator
 * @package Lego\Field\Operators
 *
 * Usage:
 *
 * $field = $filter->addSelect('floor', 'Floor')->values(['low', 'high']);
 *
 * 1、$scope is string
 *  $field->scope('height');
 *      => call model method `scopeHeight($value)`
 *      => $value could be `low` or `high`
 *
 * 2、$scope is Closure
 *  $field->scope(function (Table $filter, $value) {
 *      if ($value == 'low') {
 *          return $filter->whereLte('floor', 10);
 *      } else {
 *          return $filter->whereGt('floor', 10);
 *      }
 *  });
 *
 */
trait ScopeOperator
{
    private $scope;

    public function scope($scope)
    {
        lego_assert(is_string($scope) || $scope instanceof \Closure, 'illegal $scope');

        $this->scope = $scope;

        return $this;
    }

    public function callFilterWithScope(Table $query)
    {
        if (!$this->scope) {
            return $this->filter($query);
        }

        if (is_string($this->scope)) {
            $query->original()->{$this->scope}($this->getCurrentValue());
        } else {
            call_user_func_array($this->scope, [$query, $this->getCurrentValue()]);
        }

        return $query;
    }
}