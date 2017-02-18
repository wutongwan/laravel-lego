<?php namespace Lego\Field\Provider;

class Select extends Text
{
    protected function renderEditable()
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
