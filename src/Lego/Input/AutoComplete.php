<?php

namespace Lego\Input;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lego\Contracts\Input\MatchAble;
use Lego\Contracts\Input\Related;
use Lego\Foundation\Match\MatchQuery;
use Lego\Foundation\Match\MatchResults;
use Lego\Foundation\Response\ResponseManager;

class AutoComplete extends Input implements MatchAble, Related
{
    /**
     * 自动补全暴露给前端的请求地址
     * @var string
     */
    private $remoteUrl;

    /**
     * @var ResponseManager
     */
    private $responseManager;

    /**
     * 触发自动补全的最短长度
     * @var int
     */
    private $minInputLength = 1;

    public function __construct(ResponseManager $responseManager)
    {
        $this->responseManager = $responseManager;
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
            "Input:AutoComplete:{$this->getInputName()}",
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
                return new JsonResponse($options->all());
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

    /**
     * @return array
     * @psalm-return array{label: scalar, value: scalar}
     */
    public function getCurrentValueArray()
    {
        $value = $this->getCurrentValue();
        if (is_array($value)) {
            return array_key_exists('label', $value) && array_key_exists('value', $value)
                ? $value
                : [];
        }

        return [
            'label' => $value,
            'value' => $value,
        ];
    }
}
