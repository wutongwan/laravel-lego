<?php

namespace Lego\Widget;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

/**
 * Confirm 操作.
 */
class Confirm
{
    const CONFIRM_QUERY_NAME = '__lego_confirm';
    const FROM_QUERY_NAME = '__lego_confirm_from';

    /**
     * 提示信息.
     *
     * @var string
     */
    protected $message;

    /**
     * @var callable
     */
    protected $action;

    /**
     * 等待 $delay 秒才可确认.
     *
     * @var int
     */
    protected $delay = 0;

    /**
     * Template name
     * @var string
     */
    protected $view = 'lego::confirm';

    private $confirmQueryName;
    private $fromQueryName;

    public function __construct($message, callable $action, int $delay = null, string $view = null)
    {
        $this->message = $message;
        $this->action = $action;

        $this->confirmQueryName = self::CONFIRM_QUERY_NAME;
        $this->expectedConfirmValue = md5($message);
        $this->fromQueryName = self::FROM_QUERY_NAME;

        if ($delay) {
            $this->delay = $delay;
        }
        if ($view) {
            $this->view = $view;
        }
    }

    public function response()
    {
        $previous = URL::previous();
        parse_str(last(explode('?', $previous)), $params);
        $from = Request::query($this->fromQueryName) ?: ($params[$this->fromQueryName] ?? $previous);

        if ($confirmValue = Request::query($this->confirmQueryName)) {
            $confirmed = $confirmValue === $this->expectedConfirmValue;
            if ((new \ReflectionFunction($this->action))->getNumberOfParameters() > 0) {
                // 回调函数接收参数时, 用户的取消行为也交由回调控制
                $response = call_user_func($this->action, $confirmed);
            } elseif ($confirmed) {
                // 用户确认时, 调用回调
                $response = call_user_func($this->action);
            }
            // 其他情况返回来源页
            return isset($response) ? $response : redirect($from);
        }

        return view($this->view, [
            'message' => $this->message,
            'delay' => $this->delay,

            'confirm' => Request::fullUrlWithQuery([
                $this->confirmQueryName => $this->expectedConfirmValue,
                $this->fromQueryName => $from,
            ]),

            'cancel' => Request::fullUrlWithQuery([
                $this->confirmQueryName => 'no',
                $this->fromQueryName => $from,
            ]),
        ]);
    }
}
