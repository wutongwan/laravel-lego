<?php namespace Lego\Widget\Grid\Concerns;

use Lego\Widget\Grid\Cell;

trait HasCells
{
    /**
     * @var Cell[]
     */
    protected $cells = [];

    public function add($name, $description)
    {
        $cell = new Cell($name, $description);;
        $this->cells[$name] = $cell;
        return $cell;
    }

    /**
     * @return Cell[]
     */
    public function cells()
    {
        return $this->cells;
    }

    public function cell($name)
    {
        return $this->cells[$name];
    }
}
