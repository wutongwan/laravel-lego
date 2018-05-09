<?php namespace Lego\Field;

use Lego\Foundation\Concerns\HasHtmlAttributes;

/**
 * Field 的上层 HTML 容器，eg: div.form-group in BootStrap
 */
class FieldContainer
{
    use HasHtmlAttributes;

    // 是否隐藏
    protected $hide = false;

    public function hide($condition)
    {
        $this->hide = boolval($condition);

        return $this;
    }

    public function isHide()
    {
        return $this->hide;
    }
}
