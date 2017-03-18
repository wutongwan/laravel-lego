<?php namespace Lego\Widget\Grid;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Lego\Foundation\Exceptions\LegoException;
use Lego\LegoRegister;
use Lego\Operator\Finder;
use Lego\Operator\Store\Store;
use Lego\Register\GridCellPipe;

class Cell
{
    use Concerns\CommonPipes;

    private $name;
    private $description;
    private $pipes = [];

    private $data;
    /**
     * @var Store
     */
    private $store;
    private $default;

    function __construct($name, $description)
    {
        $pipes = explode('|', $name);
        $this->name = $pipes[0];
        $this->description = $description;

        foreach (array_slice($pipes, 1) as $pipe) {
            $this->pipe($pipe);
        }
    }

    public function name()
    {
        return $this->name;
    }

    public function description()
    {
        return $this->description;
    }

    public function default($value)
    {
        $this->default = $value;
        return $this;
    }

    /**
     * 修正函数，对现有值进行一定的修正
     *
     * $callable 可以接受两个参数
     *  - 当前值
     *  - 当前值所属的对象（ Model ）
     *
     * @param callable|string $pipe
     * @return $this
     * @throws LegoException
     */
    public function pipe($pipe)
    {
        if (is_string($pipe) && $callable = $this->getCallablePipe($pipe)) {
            $this->pipes[] = $callable;
        } elseif (is_callable($pipe)) {
            $this->pipes[] = $pipe;
        } else {
            throw new LegoException('`$pipe` is not callable');
        }

        return $this;
    }

    private function getCallablePipe($pipe)
    {
        $method = 'pipe' . ucfirst(Str::camel($pipe));
        if (method_exists($this, $method)) {
            return [$this, $method];
        } elseif ($callable = LegoRegister::get(GridCellPipe::class, $pipe)) {
            return $callable;
        } else {
            return false;
        }
    }

    public function cell($callable)
    {
        return $this->pipe($callable);
    }

    public function copy()
    {
        return clone $this;
    }

    public function fill($data)
    {
        if ($data instanceof Store) {
            $this->data = $data->getOriginalData();
            $this->store = $data;
        } else {
            $this->data = $data;
            $this->store = Finder::store($data);
        }

        return $this;
    }

    public function store()
    {
        return $this->store;
    }

    /**
     * cell original value before pipes is called
     *
     * @return string
     */
    public function getOriginalValue()
    {
        return $this->store->get($this->name);
    }

    /**
     * cell value after pipes processed
     *
     * @return HtmlString
     */
    public function value()
    {
        $value = lego_default($this->getOriginalValue(), $this->default);
        foreach ($this->pipes as $pipe) {
            $value = call_user_func_array($pipe, [$value, $this->data, $this]);
        }
        return new HtmlString((string)$value);
    }

    /**
     * cell plain value after pipes processed
     *
     * @return string
     */
    public function getPlainValue()
    {
        return strip_tags($this->value()->toHtml());
    }

    public function __toString()
    {
        return $this->value()->toHtml();
    }
}
