<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Operator\Query\Query;

class Number extends Text
{
    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $this->rule('numeric');
    }

    /**
     * Html Attributes
     *
     * @see http://www.w3schools.com/html/html_form_input_types.asp
     */
    private $min;
    private $max;
    private $step;

    public function min($value)
    {
        $this->min = $value;
        return $this->rule('min:' . $value);
    }

    public function max($value)
    {
        $this->max = $value;
        return $this->rule('max:' . $value);
    }

    public function step($value)
    {
        $this->step = $value;
        return $this;
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render()
    {
        return $this->renderByMode();
    }

    protected function renderEditable()
    {
        $type = is_integer($this->step) ? 'number' : 'text'; // iOS number field not supported float input.
        return FormFacade::input($type, $this->elementName(), $this->getDisplayValue(), [
            'id' => $this->elementId(),
            'class' => 'form-control',
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
        ]);
    }

    public function filter(Query $query)
    {
        return $this->filterWithRelationOrDirectly($query, function (Query $query) {
            return $query->whereEquals($this->column(), $this->getCurrentValue());
        });
    }
}
