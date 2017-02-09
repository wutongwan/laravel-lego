<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Data\Table\Table;
use Lego\Field\Field;

class Textarea extends Field
{
    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Table $query
     * @return Table
     */
    public function filter(Table $query)
    {
        return $query->whereContains($this->column(), $this->getCurrentValue());
    }

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
        return FormFacade::textarea(
            $this->elementName(),
            $this->getDisplayValue(),
            ['id' => $this->elementId(), 'class' => 'form-control']
        );
    }
}
