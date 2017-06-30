<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Field\Concerns\FilterWhereEquals;

class Select extends Text
{
    use FilterWhereEquals;

    protected $validateOption = true;

    protected function initialize()
    {
        parent::initialize();

        $this->validator(function ($value) {
            return !$this->validateOption || !$this->isRequired() || $this->deepInArray($value, $this->getOptions())
                ? null : '非法选项';
        });
    }

    public function disableValidateOption()
    {
        $this->validateOption = false;
        return $this;
    }

    protected function deepInArray($value, array $array)
    {
        foreach ($array as $key => $item) {
            if (is_array($item) && $this->deepInArray($value, $item)) {
                return true;
            } elseif ($value == $key) {
                return true;
            }
        }
        return false;
    }

    protected function renderReadonly()
    {
        $key = $this->takeShowValue();
        return array_key_exists($key, $this->options) ? $this->options[$key] : null;
    }

    protected function renderEditable()
    {
        if (!isset($options[null]) && is_null($this->getPlaceholder())) {
            $this->placeholder($this->description());
        }

        return FormFacade::select(
            $this->elementName(),
            $this->getOptions(),
            $this->takeInputValue(),
            $this->getAttributes()
        );
    }

    public function placeholder($placeholder = null)
    {
        $placeholder = trim($placeholder);
        return parent::placeholder($placeholder ? "* {$placeholder} *" : $placeholder);
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
}
