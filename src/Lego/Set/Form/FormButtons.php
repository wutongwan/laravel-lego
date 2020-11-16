<?php

namespace Lego\Set\Form;

use Lego\Contracts\ButtonLocations;
use Lego\Foundation\Button\Button;

/**
 * Trait FormButtons
 * @package Lego\Set\Form
 *
 * @method Button addRightTopButton(string $text, string $url = null)
 * @method Button addRightBottomButton(string $text, string $url = null)
 * @method Button addLeftTopButton(string $text, string $url = null)
 * @method Button addLeftBottomButton(string $text, string $url = null)
 * @method Button addBottomButton(string $text, string $url = null)
 */
trait FormButtons
{
    private function callAddButton(string $method, array $parameters)
    {
        $locations = [
            ButtonLocations::BTN_RIGHT_TOP,
            ButtonLocations::BTN_RIGHT_BOTTOM,
            ButtonLocations::BTN_LEFT_TOP,
            ButtonLocations::BTN_LEFT_BOTTOM,
            ButtonLocations::BOTTOM,
        ];

        $position = substr($method, 3, -6);
        if (in_array($position, $locations)) {
            return $this->buttons()->new($position, ...$parameters);
        }

        return null;
    }
}
