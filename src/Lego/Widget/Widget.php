<?php namespace Lego\Widget;

use Lego\Foundation\Operators\InitializeOperator;
use Lego\Foundation\Operators\MagicCallOperator;
use Lego\Foundation\Operators\MessageOperator;
use Lego\Foundation\Operators\RenderStringOperator;
use Lego\Register\Data\ResponseData;
use Lego\Data\Data;
use Lego\Data\Row\Row;
use Lego\Data\Table\Table;

/**
 * Lego中所有大型控件的基类
 */
abstract class Widget
{
    use MessageOperator;
    use InitializeOperator;
    use RenderStringOperator;
    use MagicCallOperator;

    // Plugins
    use Operators\FieldOperator;
    use Operators\GroupOperator;
    use Operators\RequestOperator;
    use Operators\ButtonsOperator;

    /**
     * 数据源
     * @var Data $data
     */
    private $data;

    /**
     * 响应内容
     */
    private $response;

    public function __construct($data)
    {
        $this->data = $this->prepareData($data);

        // 初始化
        $this->triggerInitialize();
    }

    abstract protected function prepareData($data): Data;

    /**
     * @return Data|Table|Row
     */
    protected function data(): Data
    {
        return $this->data;
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
         * 全局重写 Response.
         */
        $registeredResponse = ResponseData::getResponse();
        if (!is_null($registeredResponse)) {
            return $registeredResponse;
        }

        $this->processFields();
        $this->process();

        /**
         * 通过 rewriteResponse() 重写的 Response.
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

    /**
     * 默认四个方位可以插入按钮，特殊需求请重写此函数
     *
     * @return array
     */
    public function buttonLocations(): array
    {
        return ['right-top', 'right-bottom', 'left-top', 'left-bottom'];
    }
}