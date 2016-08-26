<?php namespace Lego\Widget;

use Illuminate\Support\Facades\Input;
use Lego\Field\Field;
use Lego\Source\Record\Record;

class Form extends Widget
{
    use Plugin\RecordSourcePlugin;
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
        lego_assert($this->source() instanceof Record, 'Unsupported source.');
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

    /**
     * 渲染 Response view 前调用
     */
    protected function beforeRender()
    {
        // 处理 POST 请求
        if ($this->isPost()) {
            $this->syncFieldsNewValue();
            $this->validateFields();

            if ($this->errors()->any()) {
                return null;
            }

            $this->source()->save();
            $this->messages()->add('success', '操作成功');
            return $this->successResponse();
        }

        return null;
    }

    private function validateFields()
    {
        $this->eachField(function (Field $field) {
            if ($field->validateFailed()) {
                $this->errors()->merge($field->errors());
            }
        });
    }

    /**
     * 数据处理成功后的回调
     */
    private function successResponse()
    {
        if (!$this->success) {
            return null;
        }

        if (is_callable($this->success)) {
            return call_user_func($this->success, $this->source()->data());
        }

        if (is_string($this->success)) {
            return redirect($this->success);
        }

        return null;
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render() : string
    {
        return view('lego::form.bs-horizontal', ['form' => $this])->render();
    }
}