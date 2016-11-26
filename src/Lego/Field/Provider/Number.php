<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Field\Field;

class Number extends Field
{
    /**
     * 数据处理逻辑
     */
    public function process()
    {
    }

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
    public function render(): string
    {
        return $this->renderByMode();
    }

    protected function renderEditable(): string
    {
        $type = is_integer($this->step) ? 'number' : 'text'; // iOS number field not supported float input.
        return FormFacade::input($type, $this->elementName(), $this->getCurrentValue(), [
            'id' => $this->elementId(),
            'class' => 'form-control',
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
        ]);
    }
}