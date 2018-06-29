<?php

namespace Lego\Widget\Concerns;

use Lego\Field\Field;
use Lego\Field\Group;
use Lego\Foundation\Exceptions\LegoException;

/**
 * @mixin HasFields
 */
trait HasGroups
{
    protected function initializeHasGroups()
    {
        $this->events->register('after-add-field', __CLASS__, function (Field $field) {
            foreach ($this->getActiveGroups() as $group) {
                $group->add($field->name());
            }
        });
    }

    /**
     * when $field's value = $value, call $closure add fields.
     *
     * @param Field|string $field
     * @param string       $operator
     * @param mixed        $expected
     * @param \Closure     $closure
     *
     * @return $this
     */
    public function when($field, $operator, $expected, \Closure $closure)
    {
        $field = $field instanceof Field ? $field : $this->field($field);

        $name = __METHOD__ . $field->name() . $operator . md5(json_encode($expected));

        $this->group($name, $closure);
        $this->getGroup($name)->condition($field, $operator, $expected);

        return $this;
    }

    /**
     * Group.
     */

    /**
     * Group List.
     *
     * @var Group[]
     */
    protected $groups = [];

    /**
     * @var Group[]
     */
    private $activeGroups = [];

    /**
     * @return Group[]
     */
    public function groups()
    {
        return $this->groups;
    }

    /**
     * 注意：此函数会有两种返回值，此处略奇葩，但用起来方便，若需要通过 $name 获取 Group 请使用 getGroup 函数
     *  1、传入 $callback 时，返回 Group
     *  2、反之，返回 $this ，方便链式调用.
     */
    public function group($name, \Closure $callback = null)
    {
        $group = $this->getGroup($name);

        if ($callback) {
            $this->startGroup($name);
            call_user_func_array($callback, [$this, $group]);
            $this->stopGroup($name);

            return $group;
        } else {
            $this->startGroup($name);
            $this->events->once('after-add-field', __METHOD__ . $name, function () use ($name) {
                $this->stopGroup($name);
            });

            return $this;
        }
    }

    public function getGroup($name): Group
    {
        if (isset($this->groups[$name])) {
            $group = $this->groups[$name];
        } else {
            $group = new Group($this->fields, $name);
            $this->groups[$name] = $group;
        }

        return $group;
    }

    protected function startGroup($name)
    {
        if (!isset($this->groups[$name])) {
            throw new LegoException("Group `{$name}` does not exist.");
        }

        if (isset($this->activeGroups[$name])) {
            throw new LegoException("Group `{$name}` started before.");
        }

        $this->activeGroups[$name] = $this->groups[$name];

        return $this;
    }

    /**
     * @return Group[]
     */
    protected function getActiveGroups()
    {
        return $this->activeGroups;
    }

    /**
     * @return Group
     */
    protected function currentGroup()
    {
        if ($group = array_last($this->activeGroups)) {
            return $this->getGroup($group);
        }

        return null;
    }

    protected function stopGroup($name = null)
    {
        if (is_null($name)) {
            array_pop($this->activeGroups);
        } else {
            unset($this->activeGroups[$name]);
        }

        return $this;
    }
}
