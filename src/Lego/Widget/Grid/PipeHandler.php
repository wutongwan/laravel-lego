<?php

namespace Lego\Widget\Grid;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lego\Foundation\Exceptions\LegoException;

class PipeHandler
{
    private $defaultPipes = [
        Pipes4String::class,
        Pipes4Datetime::class,
        Pipes4Features::class,
    ];

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
            $pipesClassList = array_unique(array_merge(
                $this->defaultPipes,
                Config::get('lego.widgets.grid.pipes', [])
            ));
            foreach ($pipesClassList as $pipesClass) {
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

    /**
     * @param mixed ...$arguments
     * @return mixed
     * @throws PipeBreakException
     */
    public function handle(...$arguments)
    {
        if ($this->pipe) {
            // if pipe is build in global function , pass single param only.
            if (is_callable($this->pipe) && !$this->pipe instanceof \Closure) {
                $arguments = array_slice($arguments, 0, 1);
            }

            return call_user_func_array($this->pipe, $arguments);
        }

        $class = $this->pipeClass;
        $instance = new $class(...$arguments);

        return call_user_func_array([$instance, $this->pipeClassMethod], $this->arguments);
    }
}
