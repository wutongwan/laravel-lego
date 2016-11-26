<?php namespace Lego\Field\Provider;

use Lego\Field\Field;

class Select extends Field
{
    /**
     * 数据处理逻辑
     */
    public function process()
    {
    }

    /**
     * 初始化对象
     */
    protected function initialize()
    {
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render(): string
    {
        return $this->renderByMode();
    }

    protected function renderEditable(): string
    {
        return $this->view('lego::default.field.select');
    }

    protected $options = [];

    /**
     * options(['active' => 'Active', 'disabled' => 'Disabled'])
     *
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * values([1, 2, 3]) === options([1 => 1, 2 => 2, 3 => 3])
     *
     * @param array $values
     * @return $this
     */
    public function values(array $values)
    {
        $this->options = array_combine($values, $values);

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }
}