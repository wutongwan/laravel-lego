<?php

namespace Lego\Widget\Concerns;

use Lego\Foundation\Button;

trait HasButtons
{
    protected $buttons = [];

    /**
     * 按钮位置列表，eg：[left-bottom, right-bottom, left-top, ...].
     *
     * @return array
     */
    abstract public function buttonLocations(): array;

    protected function initializeHasButtons()
    {
        foreach ($this->buttonLocations() as $location) {
            $this->buttons[$location] = [];

            self::macro(
                'add' . ucfirst(camel_case($location)) . 'Button',
                function () use ($location) {
                    $args = func_get_args();
                    array_unshift($args, $location);

                    return $this->addButton(...$args);
                }
            );
        }
    }

    public function getButtons($location)
    {
        return $this->buttons[$location];
    }

    /**
     * 根据位置和按钮文本获取按钮实例.
     *
     * 注意：此处 text 是添加按钮时提供的 text ，
     *      如果 addButton 之后通过 ->text() 重新修改了按钮文本，
     *      此处仍只能使用最初的文本获取
     *
     * @param $location
     * @param $text
     *
     * @return Button
     */
    public function getButton($location, $text)
    {
        return $this->getButtons($location)[$text];
    }

    public function addButton($location, $text, $url = null, $id = null): Button
    {
        $button = new Button($text, $url, $id);
        $this->buttons[$location][$text] = $button;
        $button->bootstrapStyle('default');

        return $button;
    }

    public function removeButton($location, $text)
    {
        unset($this->buttons[$location][$text]);
    }
}
