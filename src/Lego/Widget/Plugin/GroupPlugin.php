<?php namespace Lego\Widget\Plugin;

use Lego\Field\Group;

/**
 * Widget 中 Field 的分组功能
 *
 * Class GroupPlugin
 * @package Lego\Widget\Plugin
 */
trait GroupPlugin
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