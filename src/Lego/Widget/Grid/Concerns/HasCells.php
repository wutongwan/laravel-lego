<?php namespace Lego\Widget\Grid\Concerns;

use Lego\Foundation\Exceptions\LegoException;
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
        if ($after = $this->__once_after ?: $this->__after) {
            $idx = array_search($after, array_keys($this->cells)) + 1;
            $this->cells = array_slice($this->cells, 0, $idx, true)
                + [$name => $cell]
                + array_slice($this->cells, $idx, count($this->cells) - $idx);
            $this->__once_after = null;
        } else {
            $this->cells[$name] = $cell;
        }
        return $cell;
    }

    private $__after = null;
    private $__once_after = null;

    public function after($name, \Closure $callback = null)
    {
        if (!isset($this->cells[$name])) {
            throw new LegoException("Can not found cell `{$name}`");
        }

        if ($callback) {
            $this->__after = $name;
            call_user_func($callback, $this);
            $this->__after = null;
        } else {
            $this->__once_after = $name;
        }

        return $this;
    }

    /**
     * remove cell from grid
     */
    public function remove($names)
    {
        $names = is_array($names) ? $names : func_get_args();
        foreach ($names as $name) {
            unset($this->cells[$name]);
        }

        return $this;
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
