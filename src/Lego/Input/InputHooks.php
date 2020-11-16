<?php

namespace Lego\Input;

use Illuminate\Http\Request;
use PhpOption\Option;

/**
 * Class InputHooks
 * @package  Lego\Input
 */
class InputHooks
{
    /**
     * @var Input
     */
    protected $input;

    public function __construct(Input $input)
    {
        $this->input = $input;
    }

    /**
     * 添加到 Set 后触发
     */
    public function afterAdd()
    {
    }

    /**
     * 渲染前触发
     */
    public function beforeRender(): void
    {
    }

    /**
     * 表单提交过程触发
     */
    public function onSubmit(Request $request): void
    {
    }

    public function readOriginalValueFromAdaptor(): Option
    {
        return $this->input->getAdaptor()->getFieldValue($this->input->getFieldName());
    }

    public function readInputValueFromRequest(Request $request)
    {
        return $request->post($this->input->getInputName());
    }

    public function writeInputValueToAdaptor($value): void
    {
        $this->input->getAdaptor()->setFieldValue($this->input->getFieldName(), $value);
    }
}
