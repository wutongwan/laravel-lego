<?php

namespace Lego\Set;

use Closure;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use ReflectionFunction;

class Confirm
{
    /**
     * @var string
     */
    private $message;
    /**
     * @var int
     */
    private $delay;
    /**
     * @var Closure
     */
    private $callback;

    /**
     * @var string
     */
    private $expectedConfirmValue;

    public function __construct(string $message, Closure $callback, int $delay = 0)
    {
        $this->message = $message;
        $this->callback = $callback;
        $this->delay = $delay;

        $this->expectedConfirmValue = md5($message);
    }

    private const NAME = '__lego_confirm';
    private const FROM = '__lego_confirm_from';

    public function response(Request $request, UrlGenerator $url, Factory $view)
    {
        // 解析来源地址
        if (!$from = $request->query(self::FROM)) {
            $previous = $url->previous();
            parse_str(parse_url($previous)['query'] ?? '', $query);
            $from = $query[self::FROM] ?? $previous;
        }

        if ($confirmValue = $request->query(self::NAME)) {
            $confirmed = $confirmValue === $this->expectedConfirmValue;
            if ((new ReflectionFunction($this->callback))->getNumberOfParameters() > 0) {
                // 回调函数接收参数时, 用户的取消行为也交由回调控制
                $response = call_user_func($this->callback, $confirmed);
            } elseif ($confirmed) {
                // 用户确认时, 调用回调
                $response = call_user_func($this->callback);
            }
            // 其他情况返回来源页
            return isset($response) ? $response : redirect($from);
        }

        return $view->make('lego::bootstrap3.confirm', [
            'message' => $this->message,
            'delay' => $this->delay,

            'confirm' => $request->fullUrlWithQuery([
                self::NAME => $this->expectedConfirmValue,
                self::FROM => $from,
            ]),
            'cancel' => $request->fullUrlWithQuery([
                self::NAME => 'no',
                self::FROM => $from,
            ]),
        ]);
    }
}
