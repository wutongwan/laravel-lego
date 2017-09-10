<?php namespace Lego\Foundation\Concerns;

use Collective\Html\HtmlFacade;
use Lego\Foundation\Exceptions\LegoException;

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

    /**
     * 是否通过函数修改过模式，用于判断组件间 mode 的继承与否
     * @var bool
     */
    private $modeIsModified = false;

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

    public function modeIsModified()
    {
        return $this->modeIsModified;
    }

    public function mode($mode, $condition = true)
    {
        lego_assert(
            in_array($mode, [self::MODE_EDITABLE, self::MODE_READONLY, self::MODE_DISABLED]),
            'illegal mode'
        );

        if (value($condition)) {
            $this->mode = $mode;
            $this->modeIsModified = true;
        }

        return $this;
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

    protected function renderByMode()
    {
        return call_user_func_array([$this, 'render' . ucfirst($this->mode)], []);
    }

    protected function renderEditable()
    {
        throw new LegoException('show be rewrite.');
    }

    protected $escape = true;

    public function disableEscape()
    {
        $this->escape = false;
        return $this;
    }

    protected function renderReadonly()
    {
        $html = (string)$this->takeShowValue();

        if ($this->escape) {
            $html = htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
        }

        return HtmlFacade::tag('p', $html, [
            'id' => $this->elementId(),
            'class' => 'form-control-static',
        ]);
    }

    protected function renderDisabled()
    {
        return $this->renderReadonly();
    }
}
