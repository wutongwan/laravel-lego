<?php

namespace Lego\Widget;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Lego\DataAdaptor\EloquentAdaptor;
use Lego\Foundation\FieldName;
use Lego\Foundation\FormField;
use Lego\Input\Input;
use Lego\Input\Text;
use Lego\Lego;
use Lego\Rendering\RenderingManager;

/**
 * Class FormV2
 * @package Lego\Widget
 *
 * @method Text|FormField addText($name, $label)
 */
class FormV2 implements WidgetV2
{
    /**
     * @var Container
     */
    private $container;

    /**
     * 表单原始数据来源
     * @var mixed
     */
    private $data;

    /**
     * @var EloquentAdaptor
     */
    private $adaptor;

    /**
     * @var FormField[]|Input[]   Input 是为了方便自动补全，FormField Proxy 了 Input 的函数调用
     */
    private $fields = [];

    public function __construct(Container $container, $data)
    {
        $this->container = $container;
        $this->data = $data;
        $this->adaptor = new EloquentAdaptor($data);
    }

    public function process(Request $request)
    {
        $isPost = $request->isMethod('POST');

        // sync values from model & form input
        foreach ($this->fields as $field) {
            // sync original value from model
            $originalValue = $this->adaptor->getFieldValue($field->getFieldName());
            if ($originalValue->isDefined()) {
                $field->setOriginalValue($originalValue->get());
            }
            // sync input value from request
            if ($isPost && $field->isInputAble()) {
                $inputValue = $request->post($field->getInputName());
                if ($inputValue !== null || $originalValue->isDefined()) {
                    // 输入值不为空 or 原始值不为空，避免修改留空的字段，以便使用数据库默认值
                    $field->setInputValue($inputValue);
                }
            }
        }

        if ($isPost) {
            $this->runValidations();
            $this->saveInputValuesToModel();
        }
    }

    private function saveInputValuesToModel()
    {
        foreach ($this->fields as $field) {
            if ($field->isInputAble() && $field->issetInputValue()) {
                $this->adaptor->setFieldValue($field->getFieldName(), $field->getInputValue());
            }
        }
        $this->adaptor->save();
    }

    private function runValidations()
    {
        // run laravel rules
        $data = $rules = $customerAttributes = [];
        foreach ($this->fields as $field) {
            $name = $field->getFieldName()->getQualifiedColumnName();
            $customerAttributes[$name] = $field->getLabel();
            if ($field->isInputAble()) {
                $field->rule($field->isRequired() ? 'required' : 'nullable');
                $data[$name] = $field->getInputValue();
                $rules[$name] = $field->getRules();
            } else {
                $data[$name] = $field->getOriginalValue();
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
            if ($field->isInputAble()) {
                foreach ($field->getValidators() as $closure) {
                    try {
                        call_user_func_array($closure, [$field->getInputValue(), $data]);
                    } catch (\Exception $exception) {
                        $field->messages()->error($exception->getMessage());
                    }
                }
            }
        }
    }

    private function addField(string $inputClass, string $fieldName, string $fieldLabel)
    {
        $fieldName = new FieldName($fieldName);

        /** @var Input $input */
        $input = $this->container->make($inputClass);
        $input->setInputName($fieldName->getQualifiedColumnName());

        return $this->fields[$fieldName->getQualifiedColumnName()]
            = new FormField($input, $fieldName, $fieldLabel, $this->adaptor);
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
     * @return FormField[]|Input[]
     */
    public function getFields()
    {
        return $this->fields;
    }
}
