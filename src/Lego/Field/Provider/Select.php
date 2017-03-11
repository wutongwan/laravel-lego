<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Field\Concerns\FilterWhereEquals;

class Select extends Text
{
    use FilterWhereEquals;

    protected function renderEditable()
    {
        return FormFacade::select(
            $this->elementName(),
            $this->getOptions(),
            $this->takeDefaultInputValue(),
            $this->getAttributes()
        );
    }

    public function placeholder($placeholder = null)
    {
        return parent::placeholder('* ' . trim($placeholder) . ' *');
    }

    protected $options = [];

    /**
     * options(['active' => 'Active', 'disabled' => 'Disabled'])
     *
     * @param array $options
     * @return $this
     */
    public function options($options)
    {
        $this->options = func_num_args() > 1 ? func_get_args() : (array) $options;

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
        $values = func_num_args() > 1 ? func_get_args() : (array) $values;
        $this->options = array_combine($values, $values);

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
