<?php namespace Lego\Foundation\Operators;

use Lego\LegoException;

/**
 * 推荐宿主类实现接口 HasMode
 *
 * use 时, 必须放在 `RenderStringOperator`后面
 *
 * Class ModeOperator
 * @package Lego\Helper
 */
trait ModeOperator
{
    /**
     * 模式, eg:editable、readonly、disabled
     */
    private $mode = self::MODE_EDITABLE;

    public function getMode()
    {
        return $this->mode;
    }

    public function isMode($mode)
    {
        return $this->mode === $mode;
    }

    public function isReadonly()
    {
        return $this->isMode(self::MODE_READONLY);
    }

    public function isEditable()
    {
        return $this->isMode(self::MODE_EDITABLE);
    }

    public function isDisabled()
    {
        return $this->isMode(self::MODE_DISABLED);
    }

    public function mode($mode, $condition = true)
    {
        lego_assert(
            in_array($mode, [self::MODE_EDITABLE, self::MODE_READONLY, self::MODE_DISABLED]),
            'illegal mode'
        );

        if (value($condition)) {
            $this->mode = $mode;

            // trigger event
            $this->afterModeChanged($mode);
        }

        return $this;
    }

    /**
     * 模式变动后的回调
     */
    protected function afterModeChanged($mode)
    {
        // do nothing.
    }

    public function readonly($condition = true)
    {
        return $this->mode(self::MODE_READONLY, $condition);
    }

    public function editable($condition = true)
    {
        return $this->mode(self::MODE_EDITABLE, $condition);
    }

    public function disabled($condition = true)
    {
        return $this->mode(self::MODE_DISABLED, $condition);
    }

    protected function renderByMode() : string
    {
        return call_user_func_array([$this, 'render' . ucfirst($this->mode)], []);
    }

    protected function renderEditable() : string
    {
        throw new LegoException('show be rewrite.');
    }

    protected function renderReadonly() : string
    {
        throw new LegoException('show be rewrite.');
    }

    protected function renderDisabled() : string
    {
        throw new LegoException('show be rewrite.');
    }
}