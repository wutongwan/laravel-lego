<?php

namespace Lego\Set;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Lego\Foundation\FieldName;
use Lego\Input\AutoComplete;
use Lego\Input\Input;
use Lego\Input\Text;
use Lego\Lego;
use Lego\ModelAdaptor\EloquentAdaptor;
use Lego\ModelAdaptor\ModelAdaptor;
use Lego\Rendering\RenderingManager;
use Lego\Set\Form\FormFieldForBelongsToRelation;
use Lego\Set\Form\FormInputWrapper;

/**
 * Class Form
 * @package Lego\Widget
 *
 * @method Text|FormInputWrapper addText($name, $label)
 * @method AutoComplete|FormFieldForBelongsToRelation addAutoCompleteBelongsTo($name, $label)
 */
class Form implements Set
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var ModelAdaptor
     */
    private $adaptor;

    /**
     * @var \Lego\Set\Form\FormInputWrapper[]|Input[]   Input 是为了方便自动补全，FormInputWrapper Proxy 了 Input 的函数调用
     * @psalm-var array<string, FormInputWrapper>
     */
    private $fields = [];

    public function __construct(Container $container, $model)
    {
        $this->container = $container;
        $this->adaptor = new EloquentAdaptor($model);
    }

    public function process(Request $request)
    {
        $isSubmit = $request->isMethod('POST');

        foreach ($this->fields as $field) {
            // fill original value from adaptor
            $originalValue = $field->hooks()->readOriginalValueFromAdaptor();
            if ($accessor = $this->getAccessor()) {
                $value = $accessor($this->adaptor->getModel(), $originalValue->getOrElse(null));
                if ($value !== null) {
                    $field->values()->setOriginalValue($value);
                }
            } elseif ($originalValue->isDefined()) {
                $field->values()->setOriginalValue($originalValue->get());
            }

            // trigger input hook
            $field->hooks()->beforeRender();

            // sync input value from request (if submit)
            if ($isSubmit && $field->isInputAble()) {
                $inputValue = $request->post($field->getInputName());
                // 输入值不为空 or 原始值不为空时才填充输入值
                // 保证留空的输入框对应的字段，使用数据库默认值
                if ($inputValue !== null || $field->values()->isOriginalValueExists()) {
                    $field->values()->setInputValue($inputValue);
                }

                $field->hooks()->onSubmit($request);
            }
        }

        if ($isSubmit) {
            $this->runValidations();
            $this->saveInputValues();
        }
    }

    private function saveInputValues()
    {
        foreach ($this->fields as $field) {
            if ($field->isInputAble() && $field->values()->isInputValueExists()) {
                if ($mutator = $field->getMutator()) {
                    $mutator($this->adaptor->getModel(), $field->values()->getInputValue());
                } else {
                    $field->hooks()->writeInputValueToAdaptor($field->values()->getInputValue());
                }
            }
        }
        $this->adaptor->save();
    }

    private function runValidations()
    {
        // run laravel rules
        $data = $rules = $customerAttributes = [];
        foreach ($this->fields as $name => $field) {
            $customerAttributes[$name] = $field->getLabel();
            if ($field->isInputAble()) {
                $field->rule($field->isRequired() ? 'required' : 'nullable');
                $data[$name] = $field->values()->getInputValue();
                $rules[$name] = $field->getRules();
            } else {
                $data[$name] = $field->values()->getOriginalValue();
            }
        }
        $rulesValidator = $this->container->make(Factory::class)->make($data, $rules, [], $customerAttributes);
        if ($rulesValidator->fails()) {
            foreach ($rulesValidator->errors() as $name => $errors) {
                $this->fields[$name]->messages()->errors($errors);
            }
        }

        // run validator closures
        foreach ($this->fields as $field) {
            if ($field->isInputAble() && $field->values()->isInputValueExists()) {
                foreach ($field->getValidators() as $closure) {
                    try {
                        call_user_func_array($closure, [$field->values()->getInputValue(), $data]);
                    } catch (\Exception $exception) {
                        $field->messages()->error($exception->getMessage());
                    }
                }
            }
        }
    }

    private function addField(string $inputClass, string $name, string $label)
    {
        $fieldName = new FieldName($name);

        /** @var Input $input */
        $input = $this->container->make($inputClass);
        $input->setLabel($label);
        $input->setFieldName($fieldName);
        $input->setAdaptor($this->adaptor);

        $this->fields[$name] = $wrapper = new FormInputWrapper($input);

        $input->hooks()->afterAdd();;

        return $wrapper;
    }

    public function __call($method, $parameters)
    {
        // eg: addText(fieldName, fieldLabel)
        if (str_starts_with($method, 'add')) {
            $inputBaseClassName = substr($method, 3);
            if ($inputBaseClassName
                && class_exists($inputClass = "Lego\\Input\\{$inputBaseClassName}")
                && is_subclass_of($inputClass, Input::class)
            ) {
                return $this->addField($inputClass, $parameters[0], $parameters[1]);
            }
        }

        throw new \BadMethodCallException("Method `{$method}` not found");
    }

    public function view($view = null, $data = [], $mergeData = [])
    {
        return Lego::view($view, $data, $mergeData);
    }

    public function render()
    {
        return app(RenderingManager::class)->render($this);
    }

    /**
     * @return \Lego\Set\Form\FormInputWrapper[]|Input[]
     */
    public function getFields()
    {
        return $this->fields;
    }
}
