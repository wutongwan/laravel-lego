<?php

namespace Lego\Foundation;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Lego\Rendering\RenderingManager;

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
 * Class ResponseManger
 * @package Lego\Foundation
 */
class ResponseManger
{
    private const QUERY_NAME = '__lego_resp_id';

    /**
     * @var array<string, \Closure>
     */
    private static $handlers = [];

    public static function create(Request $request, $target, Container $container = null)
    {
        $container = $container ?: app();

        // 检查是否触发自定义 handler
        $respKey = $request->query(self::QUERY_NAME);
        if ($respKey && isset(self::$handlers[$respKey])) {
            return $container->call(self::$handlers[$respKey]);
        }

        // 默认逻辑，渲染组件
        if (method_exists($target, 'process')) {
            $container->call([$target, 'process']); // 调用组件的处理逻辑
        }
        if (is_object($target)) {
            return $container->make(RenderingManager::class)->render($target);
        } else {
            return $target;
        }
    }

    /**
     * 注册 response handler，注意需自行保证 `$key` 唯一，否则会覆盖之前的注册
     *
     * @param string $key
     * @param Closure $handler
     */
    public static function registerHandler(string $key, Closure $handler)
    {
        self::$handlers[$key] = $handler;
    }
}
