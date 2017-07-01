<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Field\Concerns\FilterWhereEquals;
use Lego\Field\Concerns\HasOptions;

class Select extends Text
{
    use FilterWhereEquals;
    use HasOptions;

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
        if (!isset($this->options[null]) && is_null($this->getPlaceholder())) {
            $this->placeholder($this->description());
        }

        $attributes = $this->getAttributes();
        if ($this->getPlaceholder() === false) {
            unset($attributes['placeholder']);
        }

        return FormFacade::select(
            $this->elementName(),
            $this->getOptions(),
            $this->takeInputValue(),
            $attributes
        );
    }

    public function placeholder($placeholder = null)
    {
        return parent::placeholder($placeholder ? "* {$placeholder} *" : $placeholder);
    }
}
