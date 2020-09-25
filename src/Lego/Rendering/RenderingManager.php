<?php

namespace Lego\Rendering;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Lego\Input\Input;
use Lego\Input\Text;
use Lego\Rendering\BootstrapV3\FormV2Render;
use Lego\Rendering\BootstrapV3\TextInputRender;
use Lego\Widget\FormV2;

class RenderingManager
{
    private const INPUTS = [
        Text::class => TextInputRender::class,
    ];

    private const WIDGETS = [
        FormV2::class => FormV2Render::class,
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

    protected function renderWidget($widget)
    {
        if ($renderClass = self::WIDGETS[get_class($widget)] ?? null) {
            return $this->container->make($renderClass)->render($widget);
        }

        throw new InvalidArgumentException('Unsupported type: ' . get_class($widget));
    }

    public function render($target)
    {
        if ($target instanceof Input) {
            return $this->renderInput($target);
        }

        if ($target instanceof FormV2) {
            return $this->renderWidget($target);
        }

        throw new InvalidArgumentException('Unsupported render type');
    }
}
