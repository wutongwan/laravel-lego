<?php namespace Lego\Foundation\Operators;

use Illuminate\Support\MessageBag;

trait MessageOperator
{
    /**
     * 所有提示信息
     * @var MessageBag
     */
    private $messages;

    /**
     * 所有错误信息
     * @var MessageBag
     */
    private $errors;

    protected function initializeMessageOperator()
    {
        $this->messages = new MessageBag();
        $this->errors = new MessageBag();
    }

    public function messages() : MessageBag
    {
        return $this->messages;
    }

    public function errors() : MessageBag
    {
        return $this->errors;
    }
}