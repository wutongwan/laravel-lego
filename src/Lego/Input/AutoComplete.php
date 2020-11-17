<?php

namespace Lego\Input;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lego\Contracts\Input\FormInput;
use Lego\Foundation\Match\MatchQuery;
use Lego\Foundation\Match\MatchResults;
use Lego\Foundation\Response\ResponseManager;

/**
 * Class AutoComplete
 * @package Lego\Input
 *
 * value type:  array{label: scalar, value: scalar}
 *
 */
class AutoComplete extends Input implements FormInput
{
    /**
     * 自动补全暴露给前端的请求地址
     * @var string
     */
    private $remoteUrl = '';

    /**
     * @var ResponseManager
     */
    private $responseManager;

    /**
     * 触发自动补全的最短长度
     * @var int
     */
    private $minInputLength = 1;
    /**
     * @var View
     */
    private $view;

    public function __construct(ResponseManager $responseManager, Factory $view)
    {
        parent::__construct();

        $this->responseManager = $responseManager;
        $this->view = $view;
    }

    public function formInputHandler()
    {
        return Form\AutoCompleteHandler::class;
    }

    /**
     * 设置自动补全回调
     *
     * @inheritDoc
     *
     * @return $this
     */
    public function match(callable $callable)
    {
        $url = $this->responseManager->registerHandler(
            sprintf("Input:%s:%s", static::class, $this->getInputName()),
            function (Request $request) use ($callable) {
                $query = new MatchQuery();
                $query->keyword = trim($request->query('__lego_auto_complete'));
                $query->setPage(intval($request->query('__lego_auto_complete_page')) ?: 1);
                if (strlen($query->keyword) === 0) {
                    return new JsonResponse([]);
                }

                $options = call_user_func_array($callable, [$query]);
                if (!$options instanceof MatchResults) {
                    $options = new MatchResults($options);
                }
                return new JsonResponse($options->jsonSerialize());
            }
        );
        $this->remoteUrl = $url . '&__lego_auto_complete=';

        return $this;
    }

    /**
     * 自动补全链接，使用 `__lego_auto_complete` query 传入自动补全关键字
     *
     * eg: $url = $field->getRemoteUrl() . '&__lego_auto_complete=关键字'
     *
     * @return string
     */
    public function getRemoteUrl(): string
    {
        return $this->remoteUrl;
    }

    /**
     * 触发自动补全的字符数
     * @param int $minInputLength
     * @return $this
     */
    public function setMinInputLength(int $minInputLength)
    {
        $this->minInputLength = $minInputLength;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinInputLength(): int
    {
        return $this->minInputLength;
    }

    public function getTextInputName()
    {
        return $this->getInputName() . '__text';
    }

    public function getTextValue()
    {
        return $this->values()->getExtra('text');
    }

    public function setTextValue($value)
    {
        $this->values()->setExtra('text', $value);
    }

    public function render()
    {
        return $this->view->make(
            'lego::bootstrap3.input.autocomplete',
            [
                'input' => $this,
            ]
        );
    }
}
