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
    use ModeOperator,
        Concerns\HasEvents;

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
                if (!$field->validate()) {
                    $this->errors()->merge($field->errors());
                }
            }
        );

        return $this;
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
        // 根据自身 mode 调整 field 的 mode
        $this->fields()->each(function (Field $field) {
            if ($this->modeIsModified() && !$field->modeIsModified()) {
                $field->mode($this->getMode());
            }

            // Field 原始值来源
            $field->setOriginalValue(
                $field->store->get($field->getColumnPathOfRelation($field->column()))
            );

            // Field 当前值来源
            $field->setNewValue(
                $this->isPost() && $field->isEditable()
                    ? Request::input($field->elementName())
                    : lego_default($field->getDefaultValue(), $field->getOriginalValue())
            );
        });

        /**
         * 处理 POST 请求
         */
        if (!$this->isPost() || !$this->isEditable()) {
            return;
        }

        // Run validation
        if ($this->validate()->errors()->any()) {
            return;
        }

        // 调用自定义的数据处理逻辑
        if ($this->submit && $response = call_user_func($this->submit, $this)) {
            $this->success($response);
            return;
        }

        // 使用默认的数据处理逻辑
        $this->syncFieldsValue();
        $this->fireEvent('saving');
        if ($this->store->save() === false) {
            $this->errors()->add('save-error', '保存失败');
            return;
        }
        $this->fireEvent('saved');

        $this->returnSuccessResponse();
        $this->messages()->add('success', '操作成功');
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
                return call_user_func($this->success, $this->data);
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
        return view(config('lego.widgets.form.default-view'), ['form' => $this])->render();
    }
}
