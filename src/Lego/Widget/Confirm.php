<?php namespace Lego\Widget;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

/**
 * Confirm 操作
 */
class Confirm
{
    /**
     * 提示信息
     * @var string
     */
    protected $message;

    /**
     * @var callable
     */
    protected $action;

    /**
     * 等待 $delay 秒才可确认
     * @var int
     */
    protected $delay = 0;

    public function __construct($message, callable $action, int $delay = null)
    {
        $this->message = $message;
        $this->action = $action;

        if ($delay) {
            $this->delay = $delay;
        }
    }

    public function response()
    {
        lego_assert(Request::isMethod('get'), __CLASS__ . ' only support GET method.');

        // 生成confirm的query参数名 & 正确值
        $confirmKey = md5($this->message);
        $expectedValue = md5($confirmKey . 'dou ni wan');
        $fromKey = '_from_' . $confirmKey;

        // 计算出来源页面
        $previous = URL::previous();
        parse_str(last(explode('?', $previous)), $params);
        $fromValue = Request::query($fromKey) ?: ($params[$fromKey] ?? $previous);

        if ($confirmValue = Request::query($confirmKey)) {
            $confirmed = $confirmValue === $expectedValue;
            $reflect = new \ReflectionFunction($this->action);
            if ($reflect->getNumberOfParameters() > 0) {
                // 回调函数接收参数时, 用户的取消行为也交由回调控制
                $response = call_user_func($this->action, $confirmed);
            } elseif ($confirmed) {
                // 用户确认时, 调用回调
                $response = call_user_func($this->action);
            }
            // 其他情况返回来源页
            return isset($response) ? $response : redirect($fromValue);
        }

        $query = [
            $confirmKey => $expectedValue,
            $fromKey => $fromValue,
        ];

        return view('lego::confirm', [
            'confirm' => Request::fullUrlWithQuery($query),
            'cancel' => Request::fullUrlWithQuery(array_merge($query, [$confirmKey => 'no'])),
            'message' => $this->message,
            'delay' => $this->delay,
        ]);
    }
}
