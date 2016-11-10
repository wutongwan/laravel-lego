<?php namespace Lego\Widget\Operators;

use Lego\Foundation\Button;

trait ButtonsOperator
{
    private $buttons = [];

    /**
     * 按钮位置列表，eg：[left-bottom, right-bottom, left-top, ...]
     * @return array
     */
    abstract public function buttonLocations(): array;

    protected function initializeButtonsOperator()
    {
        foreach ($this->buttonLocations() as $location) {
            $this->buttons[$location] = [];

            self::macro(
                'add' . ucfirst(camel_case($location)) . 'Button',
                function () use ($location) {
                    call_user_func_array([$this, 'addButton'], array_merge([$location], func_get_args()));
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