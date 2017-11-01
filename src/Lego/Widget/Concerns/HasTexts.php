<?php namespace Lego\Widget\Concerns;

trait HasTexts
{
    protected $submitText = '提交';
    protected $resetText = '清空';

    /**
     * @return string
     */
    public function getSubmitText(): string
    {
        return $this->submitText;
    }

    /**
     * Set submit button text
     *
     * @param string $submitText
     * @return $this
     */
    public function submitText(string $submitText)
    {
        $this->submitText = $submitText;
        return $this;
    }

    /**
     * @return string
     */
    public function getResetText(): string
    {
        return $this->resetText;
    }

    /**
     * Set reset button text
     *
     * @param string $resetText
     * @return $this
     */
    public function resetText(string $resetText)
    {
        $this->resetText = $resetText;
        return $this;
    }
}
