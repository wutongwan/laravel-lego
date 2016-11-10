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

    protected function registerButtonsOperatorMagicCall()
    {
        return [
            'add*Button' => function () {
                $arguments = func_get_args(); // eg: [addLeftTopButton, 'text', 'http://...', 'id']
                $arguments[0] = str_slug(snake_case(substr($arguments[0], 3, -6))); // addLeftTopButton => left-top
                return call_user_func_array([$this, 'addButton'], $arguments);
            }
        ];
    }
}