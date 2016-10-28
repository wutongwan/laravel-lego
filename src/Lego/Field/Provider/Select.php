<?php namespace Lego\Field\Provider;

//  zhrlnt@gmail.com

use Lego\Field\Field;
use Lego\Helper\HtmlUtility;
use Lego\Source\Table\Table;

class Select extends Field
{

    Use OptionsTrait;

    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Table $query
     * @return Table
     */
    public function filter(Table $query) : Table
    {
        return $query->whereEquals($this->column(), $this->value()->current());
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
        // TODO: Implement process() method.
    }

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        // TODO: Implement initialize() method.
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render() : string
    {
        // TODO: Implement render() method.
        return $this->renderByMode();
    }

    public function renderReadonly() : string
    {
        return $this->description() ?? $this->value() ?? '';
    }

    public function renderEditable() : string
    {
        return HtmlUtility::form()->select(
            $this->name(),
            $this->getOptions(),
            $this->value(),
            $this->getAttributes()
        );
    }
}
