<?php

namespace Lego\Field\Provider;

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
            $this->takeInputValue(),
            $this->getFlattenAttributes()
        );
    }

    /**
     * 数据处理逻辑.
     */
    public function process()
    {
        parent::process();

        $this->setAttribute([
            'type'  => $this->getInputType(),
            'value' => $this->takeInputValue(),
        ]);

        if ($this->isDisabled()) {
            $this->setAttribute('disabled', 'disabled');
        }
    }
}
