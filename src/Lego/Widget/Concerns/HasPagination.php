<?php namespace Lego\Widget\Concerns;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Request;

trait HasPagination
{
    /**
     * 是否有启用分页器
     * @var bool
     */
    protected $paginatorEnabled = false;

    /**
     * 分页器实例
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
    protected $paginatorPageName = 'page';

    /**
     * 分页器是否需要查询总条数
     * @var bool
     */
    protected $paginatorLengthAware = true;

    public function paginate(int $perPage, $pageName = null)
    {
        $this->paginatorEnabled = true;
        $this->paginatorPerPage = $perPage;
        $this->paginatorPageName = $pageName;
        $this->paginatorLengthAware = true;

        return $this;
    }

    public function simplePaginate(int $perPage, $pageName = null)
    {
        $this->paginatorEnabled = true;
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

    /**
     * 是否启用分页器
     */
    public function isPaginatorEnabled()
    {
        return $this->paginatorEnabled;
    }

    /**
     * 获取当前页码
     *
     * @return int
     */
    public function getPaginatorCurrentPage()
    {
        return Request::query($this->paginatorPageName, 1);
    }

    /**
     * 每页条数
     * @return int
     */
    public function getPaginatorPerPage()
    {
        return $this->paginatorPerPage;
    }
}
