<?php

namespace Lego\Set\Common;

use Lego\Foundation\Button\Buttons;

trait HasButtons
{
    /**
     * @var Buttons
     */
    private $buttons;

    private function initializeButtons()
    {
        $this->buttons = new Buttons();
    }

    public function buttons(): Buttons
    {
        return $this->buttons;
    }

    private function callAddButton(string $method, array $parameters)
    {
        if (str_starts_with($method, 'add') && str_ends_with($method, 'Button')) {
            $position = substr($method, 3, -6);
            if (in_array($position, $this->buttonLocations())) {
                return $this->buttons()->new($position, ...$parameters);
            }
        }
        return null;
    }

    abstract protected function buttonLocations(): array;
}
