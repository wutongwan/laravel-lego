<?php

namespace Lego\Set;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Lego\Contracts\ButtonLocations;
use Lego\Foundation\Button\Button;
use Lego\Foundation\Button\Buttons;
use Lego\Input\Input;
use Lego\Lego;
use Lego\ModelAdaptor\ModelAdaptor;
use Lego\ModelAdaptor\ModelAdaptorFactory;
use Lego\Rendering\RenderingManager;
use Lego\Set\Form\FormButtons;
use Lego\Set\Form\FormFields;
use Lego\Set\Form\FormInputWrapper;

/**
 * Class Form
 * @package Lego\Widget
 */
class Form implements Set
{
    use FormButtons;
    use FormFields;

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

    /**
     * @var Buttons
     */
    private $buttons;

    /**
     * @var Button
     */
    private $buttonSubmit;

    public function __construct(Container $container, ModelAdaptorFactory $factory, $model)
    {
        $this->container = $container;
        $this->adaptor = $factory->make($model);

        $this->buttons = new Buttons();
        $this->buttonSubmit = $this->buttons->new(ButtonLocations::BOTTOM, '提交');
        $this->buttonSubmit->attrs()->setAttribute('type', 'submit');
    }

    public function process(Request $request)
    {
        $isSubmit = $request->isMethod('POST');

        foreach ($this->fields as $field) {
            // fill original value from adaptor
            $this->fillOriginalValueFromAdaptor($field);

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

    /**
     * @param FormInputWrapper|Input $field
     */
    private function fillOriginalValueFromAdaptor($field)
    {
        $originalValue = $field->hooks()->readOriginalValueFromAdaptor();
        if ($accessor = $field->getAccessor()) {
            $value = $accessor($this->adaptor->getModel(), $originalValue->getOrElse(null));
            if ($value !== null) {
                $field->values()->setOriginalValue($value);
            }
        } elseif ($originalValue->isDefined()) {
            $field->values()->setOriginalValue($originalValue->get());
        }
    }

    private function saveInputValues()
    {
        $hasMutator = false;
        foreach ($this->fields as $field) {
            if ($field->isInputAble() && $field->values()->isInputValueExists() && $field->isFormOnly() === false) {
                if ($mutator = $field->getMutator()) {
                    $mutator($this->adaptor->getModel(), $field->values()->getInputValue());
                    $hasMutator = true;
                } else {
                    $field->hooks()->writeInputValueToAdaptor($field->values()->getInputValue());
                }
            }
        }

        // 触发保存到数据库
        $this->adaptor->save();

        /// mutator 会导致表单显示/提交的值和数据库中值不一致
        /// 所以需要先从 model 同步一次数据
        if ($hasMutator) {
            foreach ($this->fields as $field) {
                if ($field->isFormOnly()) {
                    continue;
                }
                $this->fillOriginalValueFromAdaptor($field);
                $values = $field->values();
                if (($original = $values->getOriginalValue()) !== $values->getInputValue()) {
                    $values->setInputValue($original);
                }
            }
        }
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


    public function __call($method, $parameters)
    {
        if (str_starts_with($method, 'add')) {
            // button, eg: addRightTopButton(text, url)
            if (str_ends_with($method, 'Button') && $btn = $this->callAddButton($method, $parameters)) {
                return $btn;
            }

            // field: eg: addText(fieldName, fieldLabel)
            if ($field = $this->callAddField($method, $parameters)) {
                return $field;
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

    public function isEditable(): bool
    {
        foreach ($this->fields as $field) {
            if (!$field->isInputAble()) {
                return false;
            }
        }
        return true;
    }

    public function buttons(): Buttons
    {
        return $this->buttons;
    }
}
