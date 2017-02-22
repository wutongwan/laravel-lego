<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Field\Concerns\FilterWhereContains;
use Lego\Field\Field;

class Text extends Field
{
    use FilterWhereContains;

    public function render()
    {
        return $this->renderByMode();
    }

    protected function renderEditable()
    {
        return FormFacade::input(
            $this->getInputType(),
            $this->elementName(),
            $this->takeDefaultInputValue(),
            $this->getAttributes()
        );
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
    }
}
