<?php

namespace Lego\Foundation\Match;

class MatchQuery
{
    /**
     * 关键字
     * @var string
     */
    public $keyword;

    /**
     * 单页数目限制
     * @var int
     */
    public $perPage = 20;

    /**
     * 页码
     * @var int
     */
    public $page = 1;

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = min(max($page, 1), 10);
    }
}
