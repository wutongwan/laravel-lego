<?php namespace Lego\Widget;

use Illuminate\Support\Facades\Request;
use Lego\Field\Field;
use Lego\Helper\HasMode;
use Lego\Helper\MagicCallOperator;
use Lego\Helper\ModeOperator;
use Lego\LegoException;
use Lego\Source\Row\Row;

/**
 * Class Form
 * @package Lego\Widget
 */
class Form extends Widget implements HasMode
{
    use ModeOperator;
    use MagicCallOperator;

    use Plugin\RequestPlugin;

    /**
     * 成功后的回调 or 跳转链接
     */
    private $success;

    /**
     * 初始化操作, 在类构造函数中调用
     */
    protected function initialize()
    {
        lego_assert($this->source() instanceof Row, 'Unsupported source.');
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
                if ($field->validateFailed()) {
                    $this->errors()->merge($field->errors());
                }
            }
        );
    }

    protected function afterModeChanged($mode)
    {
        $this->fields()->each(
            function (Field $field) use ($mode) {
                $field->mode($mode);
            }
        );
    }

    /**
     * @param Field $field
     */
    protected function fieldAdded(Field $field)
    {
        // Field 原始值来源
        $field->value()->setOriginal(
            function () use ($field) {
                return $field->source()->get($field->column());
            }
        );

        // Field 当前值来源
        $field->value()->setCurrent(
            function () use ($field) {
                if ($this->isPost() && $field->isEditable()) {
                    return Request::input($field->elementName());
                }

                return $field->value()->original();
            }
        );
    }

    public function process()
    {
        if (!$this->isPost()) {
            return;
        }

        // 处理 POST 请求
        $this->syncFieldsValue();
        $this->validate();

        if ($this->errors()->any()) {
            return;
        }

        $this->source()->save();
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
                return call_user_func($this->success, $this->source()->data());
            }

            if (is_string($this->success)) {
                return redirect($this->success);
            }

            throw new LegoException('Unsupported `success` response.');
        });
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render() : string
    {
        return view('lego::default.form.horizontal', ['form' => $this])->render();
    }
}