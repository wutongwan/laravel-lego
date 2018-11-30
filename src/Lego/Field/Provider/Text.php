<?php

namespace Lego\Field\Provider;

use Lego\Field\Field;
use Lego\Utility\HtmlUtility;

class Text extends Field
{
    protected $queryOperator = self::QUERY_CONTAINS;

    public function render()
    {
        return $this->renderByMode();
    }

    protected function renderEditable()
    {
        return HtmlUtility::input(
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
