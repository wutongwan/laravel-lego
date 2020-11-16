<?php

namespace Lego\Rendering;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Lego\Input\Input;
use Lego\Rendering\Bootstrap3\FormSetRender;
use Lego\Set\Form;
use Lego\Set\Set;

class RenderingManager
{
    private const INPUTS = [
    ];

    private const SETS = [
        Form::class => FormSetRender::class,
    ];

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function renderInput(Input $input)
    {
        if ($renderClass = self::INPUTS[get_class($input)] ?? null) {
            return $this->container->make($renderClass)->render($input);
        }

        throw new InvalidArgumentException('Unsupported type: ' . get_class($input));
    }

    protected function renderSet($set)
    {
        if ($renderClass = self::SETS[get_class($set)] ?? null) {
            return $this->container->make($renderClass)->render($set);
        }

        throw new InvalidArgumentException('Unsupported type: ' . get_class($set));
    }

    public function render($target)
    {
        if ($target instanceof Input) {
            return $this->renderInput($target);
        }

        if ($target instanceof Set) {
            return $this->renderSet($target);
        }

        throw new InvalidArgumentException('Unsupported render type');
    }
}
