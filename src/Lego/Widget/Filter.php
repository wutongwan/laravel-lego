<?php

namespace Lego\Widget;

use Lego\Field\Field;
use Lego\Widget\Grid\Grid;

/**
 * Class Filter.
 *
 * @lego-ide-helper
 */
class Filter extends Widget
{
    use Concerns\HasFields,
        Concerns\HasGroups,
        Concerns\HasInput,
        Concerns\HasBottomButtons,
        Concerns\HasQueryHelpers,
        Concerns\HasPagination;

    protected function initialize()
    {
        $this->addBottomResetButton();
    }

    /**
     * 渲染当前对象
     *
     * @return string
     */
    public function render()
    {
        return view(config('lego.widgets.filter.default-view'), ['filter' => $this]);
    }

    protected function getFieldElementNamePrefix()
    {
        return '';
    }

    /**
     * Widget 的所有数据处理都放在此函数中, 渲染 view 前调用.
     */
    public function process()
    {
        $this->processFields();

        $this->editableFields()->each(function (Field $field) {
            $field->placeholder($field->description());
            $field->setNewValue($this->getInput($field->elementName()));

            $value = $field->getNewValue();
            if (is_string($value) && !is_empty_string($value)) {
                $field->applyFilter($this->query);
            }
        });

        $this->paginator();
    }

    /** @var Grid $grid */
    protected $grid;

    public function grid($syncFields = false)
    {
        $this->grid = $this->grid ?: new Grid($this);

        if ($syncFields) {
            $this->fields()->each(
                function (Field $field) {
                    $this->grid->add($field->name(), $field->description());
                }
            );
        }

        return $this->grid;
    }

    public function getResult()
    {
        $this->processOnce();

        return $this->getQuery()->toArray();
    }
}
