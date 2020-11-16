<?php

namespace Lego\Set\Form;

use Lego\Foundation\FieldName;
use Lego\Input;
use Lego\Input\Input as BaseInput;

/**
 * Trait FormFields
 * @package Lego\Set\Form
 *
 * @method Input\Text|FormInputWrapper addText($name, $label)
 * @method Input\Hidden|FormInputWrapper addHidden($name, $label)
 * @method Input\AutoComplete|FormInputWrapper addAutoComplete($name, $label)
 * @method Input\ColumnAutoComplete|FormInputWrapper addColumnAutoComplete($name, $label)
 * @method Input\OneToOneRelation|FormInputWrapper addOneToOneRelation($name, $label)
 */
trait FormFields
{
    private function callAddField(string $method, array $parameters)
    {
        $inputBaseClassName = substr($method, 3);
        if ($inputBaseClassName
            && class_exists($inputClass = "Lego\\Input\\{$inputBaseClassName}")
            && is_subclass_of($inputClass, BaseInput::class)
        ) {
            return $this->addField($inputClass, $parameters[0], $parameters[1]);
        }

        return null;
    }

    private function addField(string $inputClass, string $name, string $label)
    {
        $fieldName = new FieldName($name);

        /** @var BaseInput $input */
        $input = $this->container->make($inputClass);
        $input->setLabel($label);
        $input->setFieldName($fieldName);
        $input->setAdaptor($this->adaptor);
        $input->setInputName($fieldName->toInputName());

        $this->fields[$name] = $wrapper = new FormInputWrapper($input);

        $input->hooks()->afterAdd();;

        return $wrapper;
    }
}
