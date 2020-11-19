<?php

namespace Lego\Set;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Confirm implements Set
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
     * @var callable
     */
    private $confirmed;

    /**
     * @var callable|null
     */
    private $canceled;

    /**
     * @var string
     */
    private $expectedConfirmValue;

    /**
     * Confirm constructor.
     * @param string $message         确认信息文案
     * @param callable $confirmed     确认后的回调
     * @param callable|null $canceled 取消后的回调
     * @param int $delay              确认按钮的延迟时间
     */
    public function __construct(string $message, callable $confirmed, callable $canceled = null, int $delay = 0)
    {
        $this->message = $message;
        $this->confirmed = $confirmed;
        $this->canceled = $canceled;

        $this->delay = $delay;
        $this->expectedConfirmValue = md5($message);
    }

    private const NAME = '__lego_confirm';
    private const FROM = '__lego_confirm_from';

    /**
     * @var Factory
     */
    private $viewFactory;

    /**
     * @var string
     */
    private $confirmUrl;

    /**
     * @var string
     */
    private $cancelUrl;

    /**
     * @var string
     */
    private $actualConfirmValue;

    public function process(Request $request, UrlGenerator $url, Factory $view)
    {
        // 解析来源地址
        if (!$from = $request->query(self::FROM)) {
            $previous = $url->previous();
            parse_str(parse_url($previous)['query'] ?? '', $query);
            $from = $query[self::FROM] ?? $previous;
        }

        $this->viewFactory = $view;
        $this->actualConfirmValue = $request->query(self::NAME);
        $this->confirmUrl = $request->fullUrlWithQuery([
            self::NAME => $this->expectedConfirmValue,
            self::FROM => $from,
        ]);
        $this->cancelUrl = $request->fullUrlWithQuery([
            self::NAME => 'no',
            self::FROM => $from,
        ]);

        if (!$this->canceled) {
            $this->canceled = function () {
                return new Response(
                    $this->viewFactory->make('lego::bootstrap3.message', ['message' => '已取消'])
                );
            };
        }
    }

    public function response()
    {
        if ($this->actualConfirmValue) {
            $callback = $this->actualConfirmValue === $this->expectedConfirmValue ? $this->confirmed : $this->canceled;
            return call_user_func_array($callback, []);
        }

        return new Response(
            $this->viewFactory->make('lego::bootstrap3.confirm', [
                'message' => $this->message,
                'delay' => $this->delay,
                'confirm' => $this->confirmUrl,
                'cancel' => $this->cancelUrl,
            ])
        );
    }
}
