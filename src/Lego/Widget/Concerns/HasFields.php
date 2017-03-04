<?php namespace Lego\Widget\Concerns;

use Illuminate\Support\Collection;

use Lego\Field\Field;
use Lego\Field\Group;
use Lego\Foundation\Event;
use Lego\Foundation\Exceptions\LegoException;
use Lego\Foundation\Facades\LegoFields;
use Lego\Foundation\Fields;

/**
 * Field 相关逻辑
 */
trait HasFields
{
    /**
     * @var Fields|Collection
     */
    protected $fields;

    protected function initializeHasFields()
    {
        $this->fields = new Fields();

        // addField Magic call
        foreach (LegoFields::all() as $name => $class) {
            self::macro('add' . $name, function () use ($class) {
                $args = func_get_args();
                return $this->addFieldByClassName($class, $args[0], $args[1] ?? null);
            });
        }
    }

    private function addFieldByClassName($class, $name, $description = null)
    {
        /** @var Field $field */
        $field = new $class($name, $description, $this->getStore());
        $field->setElementNamePrefix($this->getFieldElementNamePrefix());
        return $this->addField($field);
    }

    /**
     * 为避免同一页面有多个控件时的
     */
    abstract protected function getFieldElementNamePrefix();

    /**
     * all fields
     *
     * @return Collection|Field[]|Fields
     */
    public function fields()
    {
        return $this->fields->fields();
    }

    /**
     * only editable fields
     *
     * @return Collection|Field[]
     */
    public function editableFields()
    {
        return $this->fields()->filter(function (Field $field) {
            return $field->isEditable();
        });
    }

    /**
     * 根据 name 获取指定 Field
     * @param $fieldName
     * @return Field|null
     */
    public function field($fieldName)
    {
        return $this->fields->field($fieldName);
    }

    public function addField(Field $field): Field
    {
        $this->fields->add($field);

        foreach ($this->getActiveGroups() as $group) {
            $group->add($field->name());
        }

        Event::fire('after-add-field');

        return $field;
    }

    protected function processFields()
    {
        $this->fields()->each(function (Field $field) {
            $field->process();
        });
    }

    /**
     * Mark Fields as Required.
     *
     * @param array[]|Field[] $fields
     * @return $this
     */
    public function required($fields = [])
    {
        $fields = $fields ?: $this->fields();

        foreach ($fields as $field) {
            if (is_string($field)) {
                $this->field($field)->required();
                continue;
            }

            /** @var Field $field */
            $field->required();
        }

        return $this;
    }

    /**
     * when $field's value = $value, call $closure add fields
     *
     * @param Field|string $field
     * @param string $operator
     * @param mixed $value
     * @param \Closure $closure
     */
    public function when($field, $operator = '=', $value = null, \Closure $closure)
    {
    }

    /**
     * Group
     */

    /**
     * Group List
     * @var Group[]
     */
    protected $groups = [];
    /**
     * @var Group[]
     */
    protected $activeGroups = [];

    /**
     * 注意：此函数会有两种返回值，此处略奇葩，但用起来方便，若需要通过 $name 获取 Group 请使用 getGroup 函数
     *  1、传入 $callback 时，返回 Group
     *  2、反之，返回 $this ，方便链式调用
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
            Event::once('after-add-field', __METHOD__ . $name, function () use ($name) {
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
