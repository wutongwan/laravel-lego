<?php namespace Lego\Widget;

use Illuminate\Support\Traits\Macroable;
use Lego\Foundation\Concerns\InitializeOperator;
use Lego\Foundation\Concerns\MessageOperator;
use Lego\Foundation\Concerns\RenderStringOperator;
use Lego\Register\HighPriorityResponse;
use Lego\Widget\Concerns\ButtonLocations;

/**
 * Lego中所有大型控件的基类
 */
abstract class Widget implements ButtonLocations
{
    use MessageOperator,
        InitializeOperator,
        RenderStringOperator,
        Macroable;

    use Concerns\RequestOperator,
        Concerns\HasButtons,
        Concerns\Operable;

    public function __construct($data)
    {
        $this->initializeOperator(
            $this->transformer($data)
        );

        // 初始化 traits & self
        $this->triggerInitialize();
    }

    /**
     * 初始化 Operator 之前，对 $data 进行修正的工具函数
     */
    protected function transformer($data)
    {
        return $data;
    }

    public function data()
    {
        return $this->data;
    }

    /**
     * 默认四个方位可以插入按钮，特殊需求请重写此函数
     *
     * @return array
     */
    public function buttonLocations(): array
    {
        return [
            self::BTN_RIGHT_TOP,
            self::BTN_RIGHT_BOTTOM,
            self::BTN_LEFT_TOP,
            self::BTN_LEFT_BOTTOM,
        ];
    }

    /**
     * 对 view() 的封装
     *
     * @param $view
     * @param array $data
     * @param array $mergeData
     * @return mixed
     */
    public function view($view, $data = [], $mergeData = [])
    {
        return $this->response(view($view, $data, $mergeData));
    }

    /**
     * 响应内容
     */
    private $response;

    /**
     * 重写此次请求的 Response
     *
     * @param \Closure|string $response
     * @return $this
     */
    protected function rewriteResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Response 的封装
     *
     * @param mixed $response
     * @return mixed
     */
    final public function response($response)
    {
        /**
         * Global Response.
         */
        $registeredResponse = HighPriorityResponse::getResponse();
        if (!is_null($registeredResponse)) {
            return $registeredResponse;
        }

        $this->process();

        /**
         * if rewriteResponse() called
         */
        if (!is_null($this->response)) {
            return value($this->response);
        }

        return $response;
    }

    /**
     * Widget 的所有数据处理都放在此函数中, 渲染 view 前调用
     */
    abstract public function process();
}
