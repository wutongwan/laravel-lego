<?php

namespace Lego\Foundation\Response;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Lego\Set\Set;
use SplObjectStorage;

/**
 * Lego Response 管理器
 *
 * 调用 widget input 等对象的渲染器获取到响应数据后返回
 *  另外，也会处理特殊 input 的 ajax 请求，例如 autocomplete 等，
 *  在处理时检查请求 query 中是否包含 `__lego_resp_id` 参数，
 *  如果参数值有对应的 `handler` ，调用 handler Closure 并将其返回值作为
 *  Response 返回
 *
 *
 * Class ResponseManager
 * @package Lego\Foundation
 */
class ResponseManager
{
    private const QUERY_NAME = '__lego_resp_id';

    /**
     * @var array<string, \Closure>
     */
    private $handlers = [];

    /**
     * @var SplObjectStorage
     */
    private $sets;

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->sets = new SplObjectStorage();
    }

    public function view($view = null, $data = [], $mergeData = [])
    {
        return $this->response(
            $this->container->make(Factory::class)->make($view, $data, $mergeData)
        );
    }

    public function response($response)
    {
        // 检查是否触发自定义 handler
        $respKey = $this->container->make(Request::class)->query(self::QUERY_NAME);
        if ($respKey) {
            $respKey = urldecode($respKey);
            if (isset($this->handlers[$respKey])) {
                return $this->container->call($this->handlers[$respKey]);
            }
        }

        // 调用组件处理逻辑
        foreach ($this->sets as $set) {
            if (method_exists($set, 'process')) {
                $this->container->call([$set, 'process']); // 调用组件的处理逻辑
            }
        }

        return $response;
    }

    /**
     * 注册 response handler，注意需自行保证 `$key` 唯一，否则会覆盖之前的注册
     *
     * @param string $key
     * @param Closure $handler
     * @return string 请求链接
     */
    public function registerHandler(string $key, Closure $handler)
    {
        $this->handlers[$key] = $handler;
        return $this->container->make(Request::class)->fullUrlWithQuery([
            self::QUERY_NAME => urlencode($key),
        ]);
    }

    /**
     * 注册需要处理的组件
     *
     * @param Set $set
     */
    public function registerSet(Set $set)
    {
        $this->sets->contains($set) || $this->sets->attach($set);
    }
}
