<?php

namespace Lego\Set\Form;

use Lego\Foundation\Message\HasMessages;
use Lego\Input\Input;
use Lego\Set\Common\InputWrapper;

/**
 * Class FormInputWrapper
 * @package Lego\Foundation
 * @internal
 */
class FormInputWrapper extends InputWrapper
{
    use HasMessages,
        FormInputValidations,
        FormInputAccessorAndMutator;


    public function __construct(Input $input)
    {
        parent::__construct($input);
        $this->initializeMessages();
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
}
