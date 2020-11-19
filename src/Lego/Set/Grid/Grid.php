<?php

namespace Lego\Set\Grid;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Lego\Contracts\ButtonLocations;
use Lego\Foundation\Button\Button;
use Lego\Foundation\FieldName;
use Lego\Foundation\Response\ResponseManager;
use Lego\ModelAdaptor\ModelAdaptorFactory;
use Lego\ModelAdaptor\QueryAdaptor;
use Lego\Set\Common\HasButtons;
use Lego\Set\Set;

/**
 * Class Grid
 * @package Lego\Set\Grid
 *
 * @method Button addRightTopButton(string $text, string $url = null)
 * @method Button addRightBottomButton(string $text, string $url = null)
 * @method Button addLeftTopButton(string $text, string $url = null)
 * @method Button addLeftBottomButton(string $text, string $url = null)
 */
class Grid implements Set
{
    use HasButtons;
    use HasPagination;
    use HasBatch;

    /**
     * @var Factory
     */
    private $view;

    /**
     * @var QueryAdaptor
     */
    private $adaptor;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var ResponseManager
     */
    private $responseManager;

    public function __construct(Container $container, Factory $view, ResponseManager $responseManager, ModelAdaptorFactory $factory, $query)
    {
        $this->view = $view;
        $this->container = $container;
        $this->adaptor = $factory->makeQuery($query);
        $this->responseManager = $responseManager;

        $this->initializeButtons();
    }

    /**
     * @return QueryAdaptor
     */
    public function getAdaptor(): QueryAdaptor
    {
        return $this->adaptor;
    }

    /**
     * @var Cell[]
     */
    protected $cells = [];

    /**
     * 允许排序的字段
     * @var string[]
     */
    protected $sortAbleColumns = [];

    public function add(string $name, string $description, bool $sortAble = false): Cell
    {
        $fieldName = new FieldName($name);

        if ($sortAble) {
            if ($fieldName->getRelation()) {
                throw new \InvalidArgumentException('Cannot sort relation field: ' . $fieldName->getQualifiedColumnName());
            }
            $this->sortAbleColumns[] = $fieldName->getColumn();
        }

        return $this->cells[] = new Cell($fieldName, $description, $sortAble);
    }

    /**
     * @return Cell[]
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    protected function buttonLocations(): array
    {
        return [
            ButtonLocations::BTN_RIGHT_TOP,
            ButtonLocations::BTN_RIGHT_BOTTOM,
            ButtonLocations::BTN_LEFT_TOP,
            ButtonLocations::BTN_LEFT_BOTTOM,
        ];
    }

    public function __call($name, $arguments)
    {
        if ($btn = $this->callAddButton($name, $arguments)) {
            return $btn;
        }
        throw new \BadMethodCallException("Method `{$name}` not found");
    }

    public function process(Request $request)
    {
        if ($orders = $request->query('__lego_orders')) {
            $this->processOrders($orders);
        }

        // 从数据源查询数据
        $page = $request->query($this->paginatorPageName, 1);
        $this->paginator = $this->paginatorLengthAware
            ? $this->getAdaptor()->getLengthAwarePaginator($this->paginatorPerPage, $page)
            : $this->getAdaptor()->getPaginator($this->paginatorPerPage, $page);

        // 渲染前预处理
        $keyName = null;
        if ($hasBatch = count($this->getBatches()) > 0) {
            $keyName = $this->getBatchKeyName();
        }
        $this->rows = [];
        foreach ($this->paginator->items() as $record) {
            $row = [];
            foreach ($this->cells as $cell) {
                $row[$cell->getName()->getOriginal()] = $cell->render($record);
            }
            if ($hasBatch) {
                $row['__lego_batch_id'] = data_get($record, $keyName);
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
                    $this->getAdaptor()->orderBy($column);
                } elseif ($direction === 'desc') {
                    $this->getAdaptor()->orderBy($column, true);
                }
            }
        }
    }

    /**
     * @var array<int, array<string, string>>
     */
    protected $rows = [];

    public function getRows(): array
    {
        return $this->rows;
    }

    public function render()
    {
        return $this->view->make('lego::bootstrap3.grid-table', ['grid' => $this]);
    }
}
