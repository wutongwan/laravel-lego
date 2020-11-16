<?php

namespace Lego\Foundation\Button;

/**
 * Button container
 *
 * position name =>
 *    - button name => button instance
 *    - button name => button instance
 *    - button name => button instance
 *
 * Class Buttons
 * @package Lego\Foundation\Button
 */
class Buttons
{
    /**
     * @var array<string, array<string, Button>>
     */
    private $positions = [];

    /**
     * 添加一个按钮到指定位置
     *
     * @param string $position
     * @param string $key
     * @param Button $button
     */
    public function add(string $position, string $key, Button $button)
    {
        $this->positions[$position][$key] = $button;
    }

    public function new(string $position, string $text)
    {
        $button = new Button($text);
        $this->add($position, $text, $button);
        return $button;
    }

    /**
     * 根据位置和名称获取单个按钮
     *
     * @param string $position
     * @param string $key
     * @return Button|null
     */
    public function get(string $position, string $key)
    {
        return $this->positions[$position][$key] ?? null;
    }

    /**
     * 获取指定位置的所有按钮
     *
     * @param string $position
     * @return Button[][]|array<string, Button>
     */
    public function getByPosition(string $position): array
    {
        return $this->positions[$position] ?? [];
    }
}
