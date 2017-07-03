<?php namespace Lego\Field\Concerns;

trait HasOptions
{
    protected $options = [];

    /**
     * options(['active' => 'Active', 'disabled' => 'Disabled'])
     *
     * @param array $options
     * @return $this
     */
    public function options($options)
    {
        $this->options = func_num_args() > 1 ? func_get_args() : (array)$options;

        return $this;
    }

    /**
     * values([1, 2, 3]) === options([1 => 1, 2 => 2, 3 => 3])
     * values(1, 2, 3) === options([1 => 1, 2 => 2, 3 => 3])
     *
     * @param array|mixed $values
     * @return $this
     */
    public function values($values)
    {
        $values = func_num_args() > 1 ? func_get_args() : (array)$values;
        $this->options = array_combine($values, $values);

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 在 Select 控件中，选项有可能是嵌套数组，所以有此函数
     */
    public function getOptionLabelByValue($value)
    {
        return $this->getNestedLabel($this->options, $value);
    }

    private function getNestedLabel(array $options, $target)
    {
        foreach ($options as $key => $label) {
            if (is_array($label)) {
                return $this->getNestedLabel($label, $target);
            }

            if ($key === $target) {
                return $label;
            }
        }
        return null;
    }
}
