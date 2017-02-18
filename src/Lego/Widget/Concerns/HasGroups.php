<?php namespace Lego\Widget\Concerns;

use Lego\Field\Group;

/**
 * Widget 中 Field 的分组功能
 *
 * Class HasGroups
 * @package Lego\Widget\Plugin
 */
trait HasGroups
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
