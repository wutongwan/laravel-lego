<?php namespace Lego\Widget;

use Lego\Field\Field;
use Lego\Helper\InitializeOperator;
use Lego\Helper\MagicCallOperator;
use Lego\Helper\MessageOperator;
use Lego\Helper\RenderStringOperator;
use Lego\Register\Data\ResponseData;
use Lego\Source\Source;
use Lego\Source\Row\Row;
use Lego\Source\Table\Table;

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
    use Plugin\FieldPlugin;
    use Plugin\GroupPlugin;
    use Plugin\RequestPlugin;

    /**
     * 数据源
     * @var Source $source
     */
    private $source;

    /**
     * 响应内容
     */
    private $response;

    public function __construct($data)
    {
        $this->source = lego_source($data);

        // 初始化
        $this->triggerInitialize();
    }

    /**
     * @return Source|Table|Row
     */
    protected function source() : Source
    {
        return $this->source;
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
        $this->processFields();

        $this->process();

        /**
         * 全局重写 Response.
         */
        $registeredResponse = ResponseData::response();
        if (!is_null($registeredResponse)) {
            return $registeredResponse;
        }

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
}