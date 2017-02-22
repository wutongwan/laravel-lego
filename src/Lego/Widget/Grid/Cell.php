<?php namespace Lego\Widget\Grid;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Lego\Foundation\Exceptions\LegoException;
use Lego\Operator\Finder;
use Lego\Operator\Store\Store;

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
        return method_exists($this, $method) ? [$this, $method] : false;
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

    public function getOriginalValue()
    {
        return $this->store->get($this->name);
    }

    public function value()
    {
        $value = $this->getOriginalValue();
        foreach ($this->pipes as $cell) {
            $value = call_user_func_array($cell, [$value, $this->data]);
        }
        return new HtmlString($value);
    }

    public function __toString()
    {
        return (string)$this->value();
    }
}
