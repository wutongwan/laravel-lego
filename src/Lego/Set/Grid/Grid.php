<?php

namespace Lego\Set\Grid;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Lego\Contracts\ButtonLocations;
use Lego\Foundation\Button\Button;
use Lego\Foundation\FieldName;
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

    /**
     * @var Factory
     */
    private $view;

    public function __construct(Factory $view)
    {
        $this->view = $view;

        $this->initializeButtons();
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
    }

    /**
     * @var array<int, array<string, string>>
     */
    protected $rows = [];

    public function getRows()
    {
        return $this->rows;
    }

    public function render()
    {
        return $this->view->make('lego::bootstrap3.grid-table', ['grid' => $this]);
    }
}
