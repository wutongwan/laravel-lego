<?php namespace Lego\Widget;

use Lego\Data\Data;
use Lego\Field\Field;

class Grid extends Widget
{
    protected function prepareData($data): Data
    {
        if ($data instanceof Filter) {
            $data->processFields();
            $data->process();
        }

        return lego_table($data);
    }

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $this->data()->fetch();
    }

    /**
     * @param Field $field
     */
    protected function fieldAdded(Field $field)
    {
        $field->value()->set(function () use ($field) {
            return $field->source()->get($field->column());
        });
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render(): string
    {
        return view('lego::default.grid.table', ['grid' => $this])->render();
    }

    /**
     * Widget 的所有数据处理都放在此函数中, 渲染 view 前调用
     */
    public function process()
    {
    }

    public function rows()
    {
        return $this->data();
    }

    public function paginate(int $perPage, string $pageName = 'page', int $page = null)
    {
        $this->data()->paginate($perPage, $pageName, $page);

        return $this;
    }

    public function orderBy($attribute, bool $desc = false)
    {
        $this->data()->orderBy($attribute, $desc);
    }
}