<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Field\Field;
use Lego\Data\Table\Table;

class Text extends Field
{
    /**
     * 初始化对象
     */
    protected function initialize()
    {
    }

    public function render() : string
    {
        return $this->renderByMode();
    }

    protected function renderEditable()
    {
        return FormFacade::input(
            'text',
            $this->elementName(),
            $this->getDisplayValue(),
            $this->getAttributes()
        );
    }

    public function filter(Table $query) : Table
    {
        return $query->whereContains($this->column(), $this->getCurrentValue());
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
    }
}
