<?php namespace Lego\Helper;

/**
 * 推荐宿主类实现接口 HasMode
 * Class ModeHelper
 * @package Lego\Helper
 */
trait ModeHelper
{
    /**
     * 模式, eg:editable、readonly、disabled
     */
    private $mode = self::MODE_EDITABLE;


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
        return $this->mode(self::MODE_EDITABLE);
    }

    public function isDisabled()
    {
        return $this->isMode(self::MODE_DISABLED);
    }

    protected function mode($mode, $condition = true)
    {
        lego_assert(
            in_array($mode, [self::MODE_EDITABLE, self::MODE_READONLY, self::MODE_DISABLED]),
            'illegal mode'
        );

        if (value($condition)) {
            $this->mode = $mode;
        }

        return $this;
    }

    public function readonly($condition = true)
    {
        return $this->mode(self::MODE_READONLY, $condition);
    }

    public function editable($condition = true)
    {
        return $this->mode(self::MODE_READONLY, $condition);
    }

    public function disabled($condition = true)
    {
        return $this->mode(self::MODE_DISABLED, $condition);
    }
}