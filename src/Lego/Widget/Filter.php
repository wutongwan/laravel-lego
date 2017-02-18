<?php namespace Lego\Widget;

use Illuminate\Support\Facades\Request;
use Lego\Field\Field;

class Filter extends Widget
{
    /**
     * 初始化对象
     */
    protected function initialize()
    {
    }

    /**
     * @param Field $field
     */
    protected function fieldAdded(Field $field)
    {
        $field->placeholder($field->description());

        $field->setCurrentValue(
            Request::get($field->elementName())
        );
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render()
    {
        return view('lego::default.filter.inline', ['filter' => $this]);
    }

    /**
     * Widget 的所有数据处理都放在此函数中, 渲染 view 前调用
     */
    public function process()
    {
        $this->fields()->each(function (Field $field) {
            $value = $field->getCurrentValue();
            if ((is_string($value) && is_empty_string($value)) || !$value) {
                return;
            }

            $field->filter($this->query);
        });
    }

    /** @var Grid $grid */
    private $grid;

    public function grid($syncFields = false)
    {
        $this->grid = $this->grid ?: new Grid($this);

        if ($syncFields) {
            $this->fields()->each(
                function (Field $field) {
                    $this->grid->addField($field);
                }
            );
        }

        return $this->grid;
    }
}
