<?php

namespace Lego\Input;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lego\Contracts\RenderViewAble;
use Lego\Foundation\FieldName;
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
class AutoComplete extends Input implements RenderViewAble
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

    /**
     * @var FieldName
     */
    private $valueFieldName;

    public function __construct(ResponseManager $responseManager)
    {
        parent::__construct();

        $this->responseManager = $responseManager;
        $this->valueFieldName = $this->getFieldName()->cloneWith(
            $this->getAdaptor()->getKeyName($this->getFieldName())
        );
    }

    protected static function hooksClassName(): string
    {
        return OneToOneRelationHooks::class;
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

    public function getViewName(): string
    {
        return 'lego::bootstrap3.input.autocomplete';
    }
}
