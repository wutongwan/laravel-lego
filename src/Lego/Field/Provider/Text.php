<?php namespace Lego\Field\Provider;

use Lego\Field\Field;
use Lego\Helper\HtmlUtility;
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

    protected function renderEditable() : string
    {
        return HtmlUtility::form()->input(
            'text',
            $this->elementName(),
            $this->value()->original(),
            $this->getAttributes()
        );
    }

    protected function renderReadonly() : string
    {
        return $this->value() ?? '';
    }

    protected function renderDisabled() : string
    {
        return HtmlUtility::form()->input(
            'text',
            $this->elementName(),
            $this->value()->original(),
            ['disabled' => 'disabled']
        );
    }

    public function filter(Table $query) : Table
    {
        return $query->whereContains($this->column(), $this->value()->current());
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
    }
}