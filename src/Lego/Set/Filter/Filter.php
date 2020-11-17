<?php

namespace Lego\Set\Filter;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Lego\Contracts\ButtonLocations;
use Lego\Foundation\Button\Button;
use Lego\Foundation\FieldName;
use Lego\Input as InputNamespace;
use Lego\Input\Input;
use Lego\ModelAdaptor\ModelAdaptorFactory;
use Lego\ModelAdaptor\QueryAdaptor;
use Lego\Set\Common\HasButtons;
use Lego\Set\Common\HasFields;
use Lego\Set\Common\HasViewShortcut;
use Lego\Set\Form\FormInputWrapper;
use Lego\Set\Set;

/**
 * Class Filter
 * @package Lego\Set\Filter
 *
 * @method InputNamespace\Text|FormInputWrapper addText($name, $label)
 * @method InputNamespace\Hidden|FormInputWrapper addHidden($name, $label)
 * @method InputNamespace\AutoComplete|FormInputWrapper addAutoComplete($name, $label)
 * @method InputNamespace\ColumnAutoComplete|FormInputWrapper addColumnAutoComplete($name, $label)
 * @method InputNamespace\OneToOneRelation|FormInputWrapper addOneToOneRelation($name, $label)
 *
 * @method Button addRightTopButton(string $text, string $url = null)
 * @method Button addRightBottomButton(string $text, string $url = null)
 * @method Button addLeftTopButton(string $text, string $url = null)
 * @method Button addLeftBottomButton(string $text, string $url = null)
 */
class Filter implements Set
{
    use HasButtons;
    use HasFields;
    use HasViewShortcut;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var QueryAdaptor
     */
    private $adaptor;

    /**
     * @var Factory
     */
    private $view;

    public function __construct(Container $container, ModelAdaptorFactory $factory, Factory $view, $query)
    {
        $this->view = $view;
        $this->container = $container;
        $this->adaptor = $factory->makeQuery($query);

        $this->initializeButtons();
    }

    protected function buttonLocations(): array
    {
        return [
            ButtonLocations::BTN_RIGHT_TOP,
            ButtonLocations::BTN_RIGHT_BOTTOM,
            ButtonLocations::BTN_LEFT_TOP,
            ButtonLocations::BTN_LEFT_BOTTOM,
            ButtonLocations::BOTTOM,
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
        $input->setInputName($fieldName->toInputName());

        $this->fields[$name] = $wrapper = new FilterInputWrapper($input, $this->adaptor);
        $wrapper->handler()->afterAdd();
        return $wrapper;
    }

    /**
     * @return Input[]|FilterInputWrapper[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function __call($method, $parameters)
    {
        if ($btn = $this->callAddButton($method, $parameters)) {
            return $btn;
        }

        if ($field = $this->callAddField($method, $parameters)) {
            return $field;
        }

        throw new \BadMethodCallException("Method `{$method}` not found");
    }

    public function process(Request $request)
    {
        foreach ($this->fields as $field) {
//            dump($field->getInput()->getPlaceholder());
            if (!$field->getInput()->getPlaceholder()) {
                $field->getInput()->placeholder($field->getInput()->getLabel());
            }

            if ($field->isInputAble()) {
                $field->values()->setInputValue($request->query($field->getInputName()));
            }

            $value = $field->getInput()->values()->getCurrentValue();
            if ($scope = $field->getScope()) {
                $scope instanceof Closure ? $scope($value) : $this->adaptor->whereScope($scope, $value);
            } else {
                $field->handler()->query($field->getQueryOperator(), $value);
            }
        }
    }

    public function render()
    {
        return $this->view->make('lego::bootstrap3.filter', ['filter' => $this]);
    }
}
