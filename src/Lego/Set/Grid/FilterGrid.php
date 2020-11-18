<?php

namespace Lego\Set\Grid;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Lego\Set\Filter\Filter;

class FilterGrid extends Grid
{
    /**
     * @var Filter
     */
    private $filter;

    public function __construct(Factory $view, Filter $filter)
    {
        parent::__construct($view);

        $this->filter = $filter;
    }

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    public function process(Request $request)
    {
        if ($orders = $request->query('__lego_orders')) {
            $this->processOrders($orders);
        }

        parent::process($request);

        // 从数据库中查询数据
        $page = $request->query($this->paginatorPageName, 1);
        $this->paginator = $this->paginatorLengthAware
            ? $this->filter->getAdaptor()->getLengthAwarePaginator($this->paginatorPerPage, $page)
            : $this->filter->getAdaptor()->getPaginator($this->paginatorPerPage, $page);

        // 渲染前预处理
        $this->rows = [];
        foreach ($this->paginator->items() as $record) {
            $row = [];
            foreach ($this->cells as $cell) {
                $row[$cell->getName()->getOriginal()] = $cell->render($record);
            }
            $this->rows[] = $row;
        }
    }

    /**
     * 处理排序字段
     * @param array<string, string> $orders
     */
    private function processOrders(array $orders): void
    {
        foreach ($orders as $column => $direction) {
            if (in_array($column, $this->sortAbleColumns)) {
                if ($direction === 'asc') {
                    $this->filter->getAdaptor()->orderBy($column);
                } elseif ($direction === 'desc') {
                    $this->filter->getAdaptor()->orderBy($column, true);
                }
            }
        }
    }
}
