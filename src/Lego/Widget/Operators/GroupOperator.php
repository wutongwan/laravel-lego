<?php namespace Lego\Widget\Operators;

use Lego\Field\Group;

/**
 * Widget 中 Field 的分组功能
 *
 * Class GroupOperator
 * @package Lego\Widget\Plugin
 */
trait GroupOperator
{
    /**
     * Group List
     * @var Group[]
     */
    private $groups = [];

    public function addGroup($groupName, \Closure $callback = null) : Group
    {
        return new Group(); // TODO
    }
}