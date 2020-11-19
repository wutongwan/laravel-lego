<?php

namespace Lego\Foundation\Response;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Lego\Set\Set;
use SplObjectStorage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lego Response 管理器
 *
 * 提供了以下特性：
 *  - 通过判定 query 执行对应 handler
 *  - 高优先级响应，可在任意位置中注册 Response 覆盖默认响应行为
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
    const QUERY_NAME = '__lego_resp_id';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var array< string, callable():Response >
     */
    private $handlers = [];

    /**
     * @var SplObjectStorage|Set[]
     */
    private $pending;

    /**
     * 高优先级响应对象，优先级高于正常响应
     * @var Response|null
     */
    private $highPriorityResponses = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->pending = new SplObjectStorage();
    }

    public function view($view = null, $data = [], $mergeData = [])
    {
        return $this->response(function () use ($view, $data, $mergeData) {
            return $this->container->make(Factory::class)->make($view, $data, $mergeData);
        });
    }

    /**
     * @param callable $response
     * @return Response
     */
    public function response(callable $response)
    {
        if ($this->highPriorityResponses) {
            return $this->popHighPriorityResponse();
        }

        // 检查是否触发自定义 handler
        $respId = $this->container->make(Request::class)->query(self::QUERY_NAME);
        if ($respId && ($respId = urldecode($respId)) && array_key_exists($respId, $this->handlers)) {
            $handler = $this->handlers[$respId];
            unset($this->handlers[$respId]);
            return $this->container->call($handler);
        }

        // 响应前先尝试调用已注册组件的 process 核心逻辑
        $processed = new SplObjectStorage();
        foreach ($this->pending as $set) {
            $processed->attach($set);
            method_exists($set, 'process') && $this->container->call([$set, 'process']);
            // 检查是否有高优先级 Response
            if ($this->highPriorityResponses) {
                $this->pending->removeAll($processed);
                return $this->popHighPriorityResponse();
            }
        }
        $this->pending->removeAll($processed);

        return $this->container->call($response);
    }

    /**
     * 注册 response handler，注意需自行保证 `$key` 唯一，否则会覆盖之前的注册
     *
     * @param string $respId
     * @param callable():Response $handler
     * @return string 请求链接
     */
    public function registerHandler(string $respId, callable $handler)
    {
        $this->handlers[$respId] = $handler;
        return $this->container->make(Request::class)->fullUrlWithQuery([
            self::QUERY_NAME => urlencode($respId),
        ]);
    }

    /**
     * 注册需要处理的组件
     *
     * @param Set $set
     */
    public function registerSet(Set $set)
    {
        $this->pending->contains($set) || $this->pending->attach($set);
        return $this;
    }

    /**
     * @param Response|Closure():Response $response
     */
    public function intercept($response)
    {
        $this->highPriorityResponses[] = $response;
    }

    private function popHighPriorityResponse()
    {
        $response = array_shift($this->highPriorityResponses);
        if ($response instanceof Closure) {
            return $this->container->call($response);
        } else {
            return $response;
        }
    }

    public function reset()
    {
        $this->handlers = [];
        $this->highPriorityResponses = [];
        $this->pending = new SplObjectStorage();
    }
}
