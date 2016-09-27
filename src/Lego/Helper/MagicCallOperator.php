<?php namespace Lego\Helper;

use Lego\LegoException;

trait MagicCallOperator
{
    private $magicCalls = [];

    public function __call($name, $arguments)
    {
        foreach ($this->magicCalls() as $pattern => $closure) {
            if (!str_is($pattern, $name)) {
                continue;
            }

            return call_user_func_array($closure, array_merge([$name], $arguments));
        }

        throw new LegoException("Method `{$name}` not found.");
    }

    private function magicCalls()
    {
        if ($this->magicCalls === false) {
            return [];
        }

        if (!$this->magicCalls) {
            foreach (class_uses_recursive(static::class) as $trait) {
                $method = 'register' . class_basename($trait) . 'MagicCall';
                if (method_exists($this, $method)) {
                    $functions = call_user_func_array([$this, $method], []);

                    $this->magicCalls = array_merge($this->magicCalls, $functions);
                }
            }
        }

        return $this->magicCalls;
    }
}