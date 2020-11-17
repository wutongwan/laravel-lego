<?php

namespace Lego\Set\Filter;

use Illuminate\Contracts\Container\Container;
use Lego\Contracts\ButtonLocations;
use Lego\Foundation\FieldName;
use Lego\Input as InputNamespace;
use Lego\Input\Input;
use Lego\Set\Common\HasButtons;
use Lego\Set\Common\HasFields;
use Lego\Set\Form\FormInputWrapper;

/**
 * Class Filter
 * @package Lego\Set\Filter
 *
 * @method InputNamespace\Text|FormInputWrapper addText($name, $label)
 * @method InputNamespace\Hidden|FormInputWrapper addHidden($name, $label)
 * @method InputNamespace\AutoComplete|FormInputWrapper addAutoComplete($name, $label)
 * @method InputNamespace\ColumnAutoComplete|FormInputWrapper addColumnAutoComplete($name, $label)
 * @method InputNamespace\OneToOneRelation|FormInputWrapper addOneToOneRelation($name, $label)
 */
class Filter
{
    use HasButtons;
    use HasFields;

    /**
     * @var Container
     */
    private $container;

    private $query;

    public function __construct(Container $container, $query)
    {
        $this->container = $container;
        $this->query = $query;
    }

    protected function buttonLocations(): array
    {
        return [
            ButtonLocations::BTN_RIGHT_TOP,
            ButtonLocations::BTN_RIGHT_BOTTOM,
            ButtonLocations::BTN_LEFT_TOP,
            ButtonLocations::BTN_LEFT_BOTTOM,
        ];
    }

    /**
     * @var FilterInputWrapper[]|Input[] for ide
     * @psalm-var array<string, FilterInputWrapper[]>
     */
    private $fields;

    protected function addField($inputClass, string $name, string $label)
    {
        $fieldName = new FieldName($name);

        /** @var Input $input */
        $input = $this->container->make($inputClass);
        $input->setLabel($label);
        $input->setFieldName($fieldName);
        $input->setAdaptor($this->adaptor);
        $input->setInputName($fieldName->toInputName());

        $this->fields[$name] = $wrapper = new FormInputWrapper($input);
    }

    public function __call($method, $parameters)
    {
        if ($field = $this->callAddField($method, $parameters)) {
            return $field;
        }

        throw new \BadMethodCallException("Method `{$method}` not found");
    }
}
