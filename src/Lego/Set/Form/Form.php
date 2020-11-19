<?php

namespace Lego\Set\Form;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lego\Contracts\ButtonLocations;
use Lego\Foundation\Button\Button;
use Lego\Foundation\FieldName;
use Lego\Foundation\Response\ResponseManager;
use Lego\Input as InputNamespace;
use Lego\Input\Input;
use Lego\ModelAdaptor\ModelAdaptor;
use Lego\ModelAdaptor\ModelAdaptorFactory;
use Lego\Set\Common\HasButtons;
use Lego\Set\Common\HasFields;
use Lego\Set\Common\HasViewShortcut;
use Lego\Set\Set;

/**
 * Class Form
 * @package  Lego\Widget
 *
 * @template M
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
 * @method Button addBottomButton(string $text, string $url = null)
 */
class Form implements Set
{
    use HasButtons;
    use HasFields;
    use HasViewShortcut;

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
     * @var Button
     */
    private $buttonSubmit;

    /**
     * @var Button
     */
    private $buttonReset;

    /**
     * @var ViewFactory
     */
    private $view;

    /**
     * Form constructor.
     * @param Container $container
     * @param ModelAdaptorFactory $factory
     * @param ViewFactory $view
     * @param M $model
     */
    public function __construct(Container $container, ModelAdaptorFactory $factory, ViewFactory $view, $model)
    {
        $this->container = $container;
        $this->view = $view;
        $this->adaptor = $factory->makeModel($model);

        $this->initializeButtons();
        $this->buttonSubmit = $this->buttons->new(ButtonLocations::BOTTOM, '提交');
        $this->buttonSubmit->attrs()->setAttribute('type', 'submit');
        $this->buttonReset = $this->addBottomButton('重置');
        $this->buttonReset->attrs()->setAttribute('type', 'reset');
    }

    public function process(Request $request, ResponseManager $responseManager)
    {
        $isSubmit = $request->isMethod('POST');

        foreach ($this->fields as $field) {
            // fill original value from adaptor
            $this->fillOriginalValueFromAdaptor($field);

            // trigger input hook
            $field->handler()->beforeRender();

            // sync input value from request (if submit)
            if ($isSubmit && $field->isInputAble()) {
                $inputValue = $request->post($field->getInputName());
                // 输入值不为空 or 原始值不为空时才填充输入值
                // 保证留空的输入框对应的字段，使用数据库默认值
                if ($inputValue !== null || $field->values()->isOriginalValueExists()) {
                    $field->values()->setInputValue($inputValue);
                }

                $field->handler()->onSubmit($request);
            }
        }

        if ($isSubmit) {
            $this->runValidations();
            // 检查是否有自定义提交行为
            if ($this->submit) {
                $response = call_user_func_array($this->submit, [$this, $this->adaptor->getModel()]);
                lego_assert($response instanceof Response, "onSubmit callback must return `\Illuminate\Http\Response` instance.");
                $responseManager->intercept($response);
                return;
            } else {
                $this->saveInputValues();
            }
            // 检查是否有自定义成功行为
            if ($this->success) {
                $response = call_user_func_array($this->success, [$this->adaptor->getModel()]);
                lego_assert($response instanceof Response, "onSuccess callback must return `\Illuminate\Http\Response` instance.");
                $responseManager->intercept($response);
            }
        }
    }

    /**
     * @param FormInputWrapper|Input $field
     */
    private function fillOriginalValueFromAdaptor($field)
    {
        $originalValue = $field->handler()->readOriginalValueFromAdaptor();
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
                    $field->handler()->writeInputValueToAdaptor($field->values()->getInputValue());
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
        $rulesValidator = $this->container->make(ValidationFactory::class)->make($data, $rules, [], $customerAttributes);
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

    protected function addField(string $inputClass, string $name, string $label)
    {
        $fieldName = new FieldName($name);

        /** @var Input $input */
        $input = $this->container->make($inputClass);
        $input->setLabel($label);
        $input->setFieldName($fieldName);
        $input->setInputName($fieldName->toInputName());

        $this->fields[$name] = $wrapper = new FormInputWrapper($input, $this->adaptor);
        $wrapper->handler()->afterAdd();;

        return $wrapper;
    }

    public function __call($method, $parameters)
    {
        // button, eg: addRightTopButton(text, url)
        if ($btn = $this->callAddButton($method, $parameters)) {
            return $btn;
        }

        if ($field = $this->callAddField($method, $parameters)) {
            return $field;
        }

        throw new \BadMethodCallException("Method `{$method}` not found");
    }

    public function render()
    {
        return $this->view->make('lego::bootstrap3.form', ['form' => $this]);
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

    /**
     * @var Closure(Form, M):Response
     */
    private $submit;

    /**
     * 数据校验完成后触发，覆盖原有的写入 model 行为
     *
     * @param Closure(Form, M):Response $closure
     * @return $this
     */
    public function onSubmit(Closure $closure)
    {
        $this->submit = $closure;
        return $this;
    }

    /**
     * @var Closure(M):Response
     */
    private $success;

    /**
     * 数据写入 model 完成后触发
     * @param Closure(M):Response $closure
     */
    public function onSuccess(Closure $closure)
    {
        $this->success = $closure;
        return $this;
    }
}
