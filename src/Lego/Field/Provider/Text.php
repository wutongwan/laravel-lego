<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Field\Field;
use Lego\Operator\Query\Query;

class Text extends Field
{
    public function render()
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

    public function filter(Query $query)
    {
        return $this->filterWithRelationOrDirectly($query, function (Query $query) {
            return $query->whereContains($this->column(), $this->getCurrentValue());
        });
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
    }
}
