<?php

namespace Lego\Set\Form;

use Lego\Contracts\Input\FormInput;
use Lego\Foundation\Message\HasMessages;
use Lego\Input\Input;
use Lego\ModelAdaptor\ModelAdaptor;
use Lego\Set\Common\InputWrapper;

/**
 * Class FormInputWrapper
 * @package Lego\Foundation
 * @internal
 */
class FormInputWrapper extends InputWrapper
{
    use HasMessages,
        FormInputWrapperValidations,
        FormInputWrapperAccessorAndMutator;

    /**
     * @var FormInputHandler
     */
    private $handler;

    /**
     * @var ModelAdaptor
     */
    private $adaptor;

    public function __construct(Input $input, ModelAdaptor $adaptor)
    {
        parent::__construct($input);

        $this->adaptor = $adaptor;
        $this->initializeMessages();

        if (!$input instanceof FormInput) {
            throw new \InvalidArgumentException('Input cannot use in form: ' . get_class($input));
        }

        $handlerClass = $input->formInputHandler();
        $this->handler = new $handlerClass($input, $this);
    }

    /**
     * 输入值仅在表单使用，不同步到 model
     *
     * @var bool
     */
    private $formOnly = false;

    public function formOnly()
    {
        $this->formOnly = true;
        return $this;
    }

    public function isFormOnly(): bool
    {
        return $this->formOnly;
    }

    /**
     * @return ModelAdaptor
     */
    public function getAdaptor(): ModelAdaptor
    {
        return $this->adaptor;
    }

    public function handler(): FormInputHandler
    {
        return $this->handler;
    }
}
