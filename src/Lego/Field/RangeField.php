<?php namespace Lego\Field;

use Illuminate\Support\Facades\Request;
use Lego\Operator\Query;

abstract class RangeField extends Field
{
    const RANGE_TYPE = 'should be rewrite';

    /**
     * 上限
     * @var Field
     */
    protected $upper;

    /**
     * 下限
     * @var Field
     */
    protected $lower;

    protected function initialize()
    {
        parent::initialize();

        $class = static::RANGE_TYPE;
        $this->upper = new $class($this->name() . '-upper', $this->description(), []);
        $this->lower = new $class($this->name() . '-lower', $this->description(), []);
    }

    /**
     * @return Field
     */
    public function getLower()
    {
        return $this->lower;
    }

    public function lower(\Closure $closure)
    {
        call_user_func($closure, $this->lower);

        return $this;
    }

    /**
     * @return Field
     */
    public function getUpper()
    {
        return $this->upper;
    }

    public function upper(\Closure $closure)
    {
        call_user_func($closure, $this->upper);

        return $this;
    }

    public function process()
    {
        parent::process();

        $this->upper->setNewValue(Request::get($this->upper->elementName()));
        $this->upper->process();

        $this->lower->setNewValue(Request::get($this->lower->elementName()));
        $this->lower->process();
    }

    public function getNewValue()
    {
        $lower = $this->lower->getNewValue();
        $upper = $this->upper->getNewValue();
        return (!is_empty_string($lower) || !is_empty_string($upper)) ? [$lower, $upper] : [];
    }

    /**
     * Filter 检索数据时, 构造此字段的查询
     */
    public function filter(Query $query)
    {
        $min = $this->lower->getNewValue();
        $max = $this->upper->getNewValue();

        switch (true) {
            case !is_empty_string($min) && !is_empty_string($max):
                return $query->whereBetween($this->name(), $min, $max);

            case !is_empty_string($min):
                return $query->whereGte($this->name(), $min);

            case !is_empty_string($max):
                return $query->whereLte($this->name(), $max);

            default:
                return $query;
        }
    }

    public function render()
    {
        return $this->renderByMode();
    }

    public function renderEditable()
    {
        return $this->view('lego::default.field.range');
    }

    public function placeholder($placeholder = null)
    {
        if (is_array($placeholder)) {
            list($lower, $upper) = $placeholder;
        } else {
            $lower = $upper = $placeholder;
        }

        $this->lower->placeholder($lower);
        $this->upper->placeholder($upper);

        return $this;
    }
}
