<?php namespace Lego\Widget\Concerns;

use Lego\Foundation\Button;

trait HasButtons
{
    private $buttons = [];

    /**
     * 按钮位置列表，eg：[left-bottom, right-bottom, left-top, ...]
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
        lego_assert(in_array($location, $this->buttonLocations()), "{$location} does not exists.");
        return $this->buttons[$location];
    }

    public function addButton($location, $text, $url = null, $id = null)
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
