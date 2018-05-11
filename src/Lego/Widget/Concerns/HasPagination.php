<?php namespace Lego\Widget\Concerns;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Request;

trait HasPagination
{
    /**
     * 翻页器实例
     * @var AbstractPaginator
     */
    protected $paginator;

    /**
     * how many rows per page
     * @var int
     */
    protected $paginatorPerPage = 100;

    /**
     * 分页器 GET 参数，eg：page
     * @var string
     */
    protected $paginatorPageName;

    /**
     * 分页器是否需要查询总条数
     * @var bool
     */
    protected $paginatorLengthAware = true;

    public function paginate(int $perPage, $pageName = null)
    {
        $this->paginatorPerPage = $perPage;
        $this->paginatorPageName = $pageName;
        $this->paginatorLengthAware = true;

        return $this;
    }

    public function simplePaginate(int $perPage, $pageName = null)
    {
        $this->paginatorPerPage = $perPage;
        $this->paginatorPageName = $pageName;
        $this->paginatorLengthAware = false;

        return $this;
    }

    public function paginator()
    {
        if (!$this->paginator) {
            $this->paginator = $this->query->paginate(
                $this->paginatorPerPage,
                null,
                $this->paginatorPageName,
                null,
                $this->paginatorLengthAware
            );
            $this->paginator->appends(Request::input());
        }

        return $this->paginator;
    }
}
