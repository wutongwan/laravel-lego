<?php

namespace Lego\Set\Grid;

use Illuminate\Contracts\Pagination\Paginator;

trait HasPagination
{
    /**
     * 分页器实例.
     *
     * @var Paginator
     */
    protected $paginator;

    /**
     * how many rows per page.
     *
     * @var int
     */
    protected $paginatorPerPage = 100;

    /**
     * 分页器 GET 参数，eg：page.
     *
     * @var string
     */
    protected $paginatorPageName = 'page';

    /**
     * 分页器是否需要查询总条数.
     *
     * @var bool
     */
    protected $paginatorLengthAware = true;

    public function paginate(int $perPage, $pageName = 'page')
    {
        $this->paginatorPerPage = $perPage;
        $this->paginatorPageName = $pageName;
        $this->paginatorLengthAware = true;

        return $this;
    }

    public function simplePaginate(int $perPage, $pageName = 'page')
    {
        $this->paginatorPerPage = $perPage;
        $this->paginatorPageName = $pageName;
        $this->paginatorLengthAware = false;

        return $this;
    }

    /**
     * @return Paginator
     */
    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }
}
