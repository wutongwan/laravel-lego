<?php

namespace Lego\Contracts\Input;

/**
 * 需要传入 match 方式的 input
 *
 * Interface MatchAble
 * @package Lego\Contracts
 */
interface MatchAble
{

    /**
     * Set match callback
     *
     * @param callable $callable 自动补全回调，根据关键字返回 kv 补全结果
     *
     * @psalm-param callable(MatchQuery):MatchResults|array<string, string> $callable
     *
     * @return mixed
     */
    public function match(callable $callable);
}
