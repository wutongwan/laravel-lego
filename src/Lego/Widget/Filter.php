<?php namespace Lego\Widget;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Lego\Field\Field;

class Filter extends Widget
{
    public function __construct($data)
    {
        if (is_subclass_of($data, Model::class)) {
            $data = new $data;
        }

        if ($data instanceof Model) {
            $data = $data->newQuery();
        }

        parent::__construct($data);
    }

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
        $field->value()->set(function () use ($field) {
            return Request::get($field->elementName());
        });
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render() : string
    {
        return view('lego::default.filter.inline', ['filter' => $this])->render();
    }

    /**
     * Widget 的所有数据处理都放在此函数中, 渲染 view 前调用
     */
    public function process()
    {
        $this->fields()->each(function (Field $field) {
            $field->filter($this->source());
        });
    }

    public function grid()
    {
        return new Grid($this);
    }
}