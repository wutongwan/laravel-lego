<?php namespace Lego\Field\Provider;

use Lego\Operator\Query\Query;

class Number extends Text
{
    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $this->attr('step', 1);
        $this->rule('numeric');
    }

    /**
     * Html Attributes
     *
     * @see http://www.w3schools.com/html/html_form_input_types.asp
     */

    public function min($value)
    {
        $this->attr('min', $value);
        return $this->rule('min:' . $value);
    }

    public function max($value)
    {
        $this->attr('max', $value);
        return $this->rule('max:' . $value);
    }

    public function step($value)
    {
        $this->attr('step', $value);
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
        // iOS number field not supported float input.
        $this->inputType = is_integer($this->getAttribute('step')) ? 'number' : 'text';

        return parent::renderEditable();
    }

    public function filter(Query $query)
    {
        return $this->filterWithRelation($query, function (Query $query) {
            return $query->whereEquals($this->column(), $this->getNewValue());
        });
    }
}
