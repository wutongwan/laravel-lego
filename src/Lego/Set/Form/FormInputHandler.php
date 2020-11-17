<?php

namespace Lego\Set\Form;

use Illuminate\Http\Request;
use Lego\Input\Input;
use PhpOption\Option;

abstract class FormInputHandler
{
    /**
     * @var FormInputWrapper
     */
    protected $wrapper;

    /**
     * @var Input
     */
    protected $input;

    public function __construct(Input $input, FormInputWrapper $wrapper)
    {
        $this->wrapper = $wrapper;
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

    /**
     * 从 model 中读取原始值
     *
     * @return Option
     */
    public function readOriginalValueFromAdaptor(): Option
    {
        return $this->wrapper->getAdaptor()->getFieldValue($this->input->getFieldName());
    }

    /**
     * 从请求对象中获取最新值
     * @param Request $request
     * @return array|string|null
     */
    public function readInputValueFromRequest(Request $request)
    {
        return $request->post($this->input->getInputName());
    }

    /**
     * 将最新值写回 model
     * @param $value
     */
    public function writeInputValueToAdaptor($value): void
    {
        $this->wrapper->getAdaptor()->setFieldValue($this->input->getFieldName(), $value);
    }
}
