<?php namespace Lego\Widget\Grid;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lego\Foundation\Exceptions\LegoException;

class PipeHandler
{
    private $pipe;
    private $pipeClass;
    private $pipeClassMethod;
    private $arguments = [];

    protected static $registered = [];

    public function __construct($pipe, $arguments = [])
    {
        if ($pipe instanceof \Closure) {
            $this->pipe = $pipe;
            return;
        }

        if (empty($arguments) && Str::contains($pipe, ':')) {
            list($pipe, $argumentsString) = explode(':', $pipe, 2);
            $this->arguments = explode(',', $argumentsString);
        } else {
            $this->arguments = $arguments;
        }

        if ($collect = $this->getPipeClassAndMethod($pipe)) {
            list($this->pipeClass, $this->pipeClassMethod) = $collect;
        } elseif (is_callable($pipe)) {
            $this->pipe = $pipe;
        } else {
            throw new LegoException('illegal $pipe');
        }
    }

    protected function getPipeClassAndMethod($pipe)
    {
        if (empty(static::$registered)) {
            foreach (Config::get('lego.widgets.grid.pipes', []) as $pipesClass) {
                $rft = new \ReflectionClass($pipesClass);
                foreach ($rft->getMethods() as $method) {
                    if (Str::startsWith($method->name, 'handle')) {
                        $name = substr($method->name, 6);
                        static::$registered[Str::snake($name, '-')] = [$pipesClass, $method->name];
                    }
                }
            }
        }

        return isset(static::$registered[$pipe]) ? static::$registered[$pipe] : false;
    }

    public static function forgetRegistered()
    {
        static::$registered = [];
    }

    public function handle(...$arguments)
    {

        if ($this->pipe) {
            return call_user_func_array($this->pipe, $arguments);
        }

        $class = $this->pipeClass;
        $instance = new $class(...$arguments);
        return call_user_func_array([$instance, $this->pipeClassMethod], $this->arguments);
    }
}
