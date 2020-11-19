<?php

namespace Lego\Foundation\Response;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * @var Container
     */
    private $container;

    /**
     * @var array<string, \Closure>
     */
    private $handlers = [];

    /**
     * @var SplObjectStorage|Set[]
     */
    private $sets;

    /**
     * @var SplObjectStorage
     */
    private $processed;

    /**
     * 高优先级响应对象，优先级高于正常响应
     * @var Response|null
     */
    private $intercept;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->sets = new SplObjectStorage();
        $this->processed = new SplObjectStorage();
    }

    public function view($view = null, $data = [], $mergeData = [])
    {
        return $this->response(
            $this->container->make(Factory::class)->make($view, $data, $mergeData)
        );
    }

    public function response($response)
    {
        if ($this->intercept) {
            return $this->intercept;
        }

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
            $this->process($set);
            if ($this->intercept) {
                return $this->intercept;
            }
        }

        if ($this->intercept) {
            return $this->intercept;
        }
        return $response;
    }

    public function process($instance)
    {
        if ($this->processed->contains($instance)) {
            return;
        }

        if (method_exists($instance, 'process')) {
            $this->container->call([$instance, 'process']);
        }
        $this->processed->attach($instance);
    }

    /**
     * 注册 response handler，注意需自行保证 `$key` 唯一，否则会覆盖之前的注册
     *
     * @param string $key
     * @param callable $handler
     * @return string 请求链接
     */
    public function registerHandler(string $key, callable $handler)
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

    public function intercept(Response $response)
    {
        $this->intercept = $response;
    }

    public function reset()
    {
        $this->intercept = null;
        $this->handlers = [];
        $this->sets = new SplObjectStorage();
        $this->processed = new SplObjectStorage();
    }
}
