<?php namespace Lego\Widget;

use Illuminate\Support\Facades\Request;
use Lego\Field\Field;
use Lego\Widget\Grid\Grid;

/**
 * Class Filter
 * @lego-ide-helper
 * @package Lego\Widget
 */
class Filter extends Widget
{
    use Concerns\HasFields,
        Concerns\HasGroups,
        Concerns\HasBottomButtons;

    protected function initialize()
    {
        $this->addBottomResetButton();
    }

    /**
     * 渲染当前对象
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
     * Widget 的所有数据处理都放在此函数中, 渲染 view 前调用
     */
    public function process()
    {
        $this->processFields();

        $this->editableFields()->each(function (Field $field) {
            $field->placeholder($field->description());
            $field->setNewValue(Request::query($field->elementName()));

            $value = $field->getNewValue();
            if ((is_string($value) && is_empty_string($value)) || !$value) {
                return;
            }

            $field->applyFilter($this->query);
        });
    }

    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $this->query->with($relations);
        return $this;
    }

    /** @var Grid $grid */
    private $grid;

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
}
