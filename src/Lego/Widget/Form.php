<?php namespace Lego\Widget;

use Illuminate\Support\Facades\Request;
use Lego\Field\Field;
use Lego\Foundation\Concerns\HasMode;
use Lego\Foundation\Concerns\ModeOperator;

/**
 * Class Form
 * @package Lego\Widget
 */
class Form extends Widget implements HasMode
{
    use ModeOperator;
    use Concerns\EloquentOperator;

    /**
     * 此属性设置后将不再调用默认的数据处理逻辑
     * @var \Closure
     */
    private $submit;

    /**
     * 成功后的回调 or 跳转链接
     */
    private $success;

    /**
     * 初始化操作, 在类构造函数中调用
     */
    protected function initialize()
    {
    }

    /**
     * 保存成功后的 Response
     * @param \Closure|string $success 回调 or 跳转链接
     * @return static
     */
    public function success($success)
    {
        $this->success = $success;

        return $this;
    }

    private function validate()
    {
        $this->fields()->each(
            function (Field $field) {
                if (!$field->validationPassed()) {
                    $this->errors()->merge($field->errors());
                }
            }
        );
    }

    /**
     * @param Field $field
     */
    protected function fieldAdded(Field $field)
    {
        if ($this->modeIsModified() && !$field->modeIsModified()) {
            $field->mode($this->getMode());
        }

        // Field 原始值来源
        $field->setOriginalValue(
            $field->store->get($field->column())
        );

        // Field 当前值来源
        $field->setCurrentValue(
            $this->isPost() && $field->isEditable()
                ? Request::input($field->elementName())
                : $field->getDefaultValue($field->getOriginalValue())
        );

        $field->setDisplayValue($field->getCurrentValue());
    }


    /**
     * 通过此函数传入数据处理逻辑，使用此函数后，将不再调用默认的数据处理逻辑（保存到 Model）
     *
     * @param \Closure $closure
     * @return $this
     */
    public function onSubmit(\Closure $closure)
    {
        $this->submit = $closure;

        return $this;
    }

    public function process()
    {
        if (!$this->isPost() || !$this->isEditable()) {
            return;
        }

        /**
         * 处理 POST 请求
         */

        $this->validate();
        if ($this->errors()->any()) {
            return;
        }

        if ($this->submit) {
            // 调用自定义的数据处理逻辑
            if ($response = call_user_func($this->submit, $this)) {
                $this->success($response);
                return;
            }
        } else {
            // 使用默认的数据处理逻辑
            $this->syncFieldsValue();
            if ($this->data()->save() === false) {
                $this->errors()->add('save-error', '保存失败');
                return;
            }
        }

        $this->messages()->add('success', '操作成功');
        $this->returnSuccessResponse();
    }

    /**
     * 数据处理成功后的回调
     */
    private function returnSuccessResponse()
    {
        if (!$this->success) {
            return;
        }

        $this->rewriteResponse(function () {
            if (is_callable($this->success)) {
                return call_user_func($this->success, $this->data()->original());
            }

            if (is_string($this->success) && starts_with($this->success, ['http://', '/'])) {
                return redirect($this->success);
            }

            return $this->success;
        });
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render()
    {
        return view('lego::default.form.horizontal', ['form' => $this])->render();
    }
}
