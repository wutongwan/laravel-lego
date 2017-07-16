<?php namespace Lego\Widget;

use Illuminate\Support\Facades\Request;
use Lego\Field\Field;
use Lego\Foundation\Concerns\HasMode;
use Lego\Foundation\Concerns\ModeOperator;
use Lego\Register\HighPriorityResponse;

/**
 * Class Form
 * @method \Lego\Foundation\Button addRightTopButton($text, $url = null, $id = null)
 * @method \Lego\Foundation\Button addRightBottomButton($text, $url = null, $id = null)
 * @method \Lego\Foundation\Button addLeftTopButton($text, $url = null, $id = null)
 * @method \Lego\Foundation\Button addLeftBottomButton($text, $url = null, $id = null)
 * @package Lego\Widget
 */
class Form extends Widget implements HasMode
{
    use ModeOperator,
        Concerns\HasFields,
        Concerns\HasGroups,
        Concerns\HasEvents;

    private $action;

    /**
     * 此属性设置后将不再调用默认的数据处理逻辑
     * @var \Closure
     */
    private $submit;

    /**
     * 成功后的回调、跳转链接、任意 Response 内容
     */
    private $success;

    public function action($url)
    {
        if ($url instanceof \Closure) {
            $this->action = HighPriorityResponse::register(__METHOD__, $url);
        } else {
            $this->action = $url;
        }

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function elementId()
    {
        return 'lego-form-' . md5($this->action);
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
        $data = [];
        foreach ($this->editableFields() as $field) {
            $data[$field->column()] = $field->getNewValue();
            $data[$field->elementName()] = $field->getNewValue();
        }
        $data = array_merge($this->store->toArray(), $data);

        $this->editableFields()->each(function (Field $field) use ($data) {
            if (!$field->validate($data)) {
                $this->errors()->merge($field->errors());
            }
        });

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

    protected function getFieldElementNamePrefix()
    {
        return 'lego-form-';
    }

    public function process()
    {
        $this->processFields();
        $this->syncFieldsValueFromStore();
        // 根据自身 mode 调整 field 的 mode
        $this->fields()->each(function (Field $field) {
            if ($this->modeIsModified() && !$field->modeIsModified()) {
                $field->mode($this->getMode());
            }
        });

        /**
         * 下面处理 POST 请求
         */
        if ($this->isPost() && $this->isEditable() && $this->shouldAction()) {
            // Sync fields from request
            $this->fields()->each(function (Field $field) {
                $field->setNewValue(Request::input($field->elementName()));
            });

            // Run validation
            if ($this->validate()->errors()->any()) {
                return;
            }

            // Call custom submit action <if submit defined>
            if ($this->submit && $response = call_user_func($this->submit, $this)) {
                $this->success($response);
                $this->returnSuccessResponse();
                return;
            }

            // Save to store <default>
            if ($this->saveFieldsValueToStore()) {
                $this->returnSuccessResponse();
                $this->messages()->add('success', '操作成功');
            } else {
                $this->errors()->add('error', '保存失败');
            }
        }
    }

    /**
     * sync field's value to source.
     */
    private function saveFieldsValueToStore()
    {
        $this->editableFields()->each(function (Field $field) {
            $field->syncValueToStore();
        });

        $this->fireEvent('saving');
        if ($this->getStore()->save()) {
            $this->syncFieldsValueFromStore();
            $this->fireEvent('saved');
            return true;
        }

        return false;
    }

    private function shouldAction()
    {
        return !$this->action || Request::fullUrl() === $this->action;
    }

    /**
     * Sync field's original from store
     */
    private function syncFieldsValueFromStore()
    {
        $this->fields()->each(function (Field $field) {
            $field->setOriginalValue(
                $field->getStore()->get($field->getColumnPathOfRelation($field->column()))
            );
        });
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
            } elseif (is_string($this->success) && starts_with($this->success, ['http://', '/'])) {
                return redirect($this->success);
            } else {
                return $this->success;
            }
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
