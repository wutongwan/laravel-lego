<?php namespace Lego\Field;

use Illuminate\Support\Facades\Request;
use Lego\Operator\Query\Query;

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
        $this->upper->setNewValue(Request::get($this->upper->elementName()));
        $this->upper->process();

        $this->lower->setNewValue(Request::get($this->lower->elementName()));
        $this->lower->process();
    }

    public function getNewValue()
    {
        $lower = $this->lower->getNewValue();
        $upper = $this->lower->getNewValue();
        return ($lower || $upper) ? [$lower, $upper] : [];
    }

    /**
     * Filter 检索数据时, 构造此字段的查询
     */
    public function filter(Query $query)
    {
        return $this->filterWithRelation($query, function (Query $query) {
            $min = $this->lower->getNewValue();
            $max = $this->upper->getNewValue();

            switch (true) {
                case !is_null($min) && !is_null($max):
                    return $query->whereBetween($this->column(), $min, $max);

                case !is_null($min):
                    return $query->whereGte($this->column(), $min);

                case !is_null($max):
                    return $query->whereLte($this->column(), $max);

                default:
                    return $query;
            }
        });
    }

    public function render()
    {
        return $this->renderByMode();
    }

    public function renderEditable()
    {
        return $this->view('lego::default.field.range');
    }
}
