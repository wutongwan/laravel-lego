<?php namespace Lego\Widget\Concerns;

use Illuminate\Support\Facades\URL;
use Lego\Foundation\Button;

/**
 * 底部按钮区
 */
trait HasBottomButtons
{
    protected $bottomLocation = 'bottom'; // 底部按钮区名称

    protected $submitButtonKey = 'submit';
    protected $resetButtonKey = 'reset';

    // 注册到按钮区列表
    public function buttonLocations(): array
    {
        $locations = parent::buttonLocations();
        $locations[] = $this->bottomLocation;
        return $locations;
    }

    public function getBottomButtons()
    {
        return $this->getButtons($this->bottomLocation);
    }

    protected function initializeHasBottomButtons()
    {
        /**
         * Add submit button
         * @var Button $btn
         */
        $btn = $this->addButton($this->bottomLocation, $this->submitButtonKey);
        $btn->bootstrapStyle('primary')
            ->text('提交')
            ->attribute('type', 'submit');
    }

    /**
     * Add reset Button
     */
    protected function addBottomResetButton()
    {
        /** @var Button $btn */
        $btn = $this->addButton($this->bottomLocation, $this->resetButtonKey, URL::full());
        $btn->text('清空');
    }

    /**
     * Set submit button text
     *
     * @param string $submitText
     * @return $this
     */
    public function submitText(string $submitText)
    {
        /** @var Button $btn */
        $btn = $this->getButton($this->bottomLocation, $this->submitButtonKey);
        $btn->text($submitText);
        return $this;
    }

    /**
     * Set reset button text
     *
     * @param string $resetText
     * @return $this
     */
    public function resetText(string $resetText)
    {
        /** @var Button $btn */
        $btn = $this->getButton($this->bottomLocation, $this->resetButtonKey);
        $btn->text($resetText);
        return $this;
    }
}
