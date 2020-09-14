<?php

namespace Lego\Field\Concerns;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Field 中数据承载部分.
 *
 * - set ：填充原始属性值，不推荐重写
 *
 * - get ：读取原始属性值，不推荐重写
 *
 * - take ：常用场景值的调用部分
 *      - 例如：takeInputValue() ，用于返回用户输入时的默认值，
 *              此默认值可能是数据库中的存储值，也有可能是开发者通过接口函数传入的值，
 *              此函数会根据优先级不同，返回特定的值
 *      - 如果需要重写时，先考虑下是否可以通过已有的 mutate 系列函数实现
 *
 * - mutate ：对值的改正逻辑，接受参数，不依赖属性
 *      - 例如：mutateSavingValue($value) ：数据入库前将通过此函数进行必要修正
 */
trait HasValues
{
    /**
     * 原始值，一般读取自数据库等数据源.
     */
    protected $originalValue;

    /**
     * 新值，一般为当前请求中产生的值
     */
    protected $newValue;

    /**
     * 展示值，仅用于展示.
     */
    protected $displayValue;

    /**
     * 默认值
     */
    protected $defaultValue;

    protected $doesntStore = false;

    /**
     * 禁用 HTML Purifier.
     *
     * @var bool
     */
    protected $disablePurifier = false;

    /**
     * HTML purifier config.
     */
    protected $purifierConfig = [
        'HTML.Allowed' => '',
        'AutoFormat.RemoveEmpty' => true,
        'AutoFormat.AutoParagraph' => false,
    ];

    /**
     * @var HTMLPurifier
     */
    private $purifier;

    protected function initializeHasValues()
    {
        $purifierConfig = HTMLPurifier_Config::createDefault();
        $purifierConfig->loadArray($this->purifierConfig);
        $this->purifier = new HTMLPurifier($purifierConfig);
    }

    /**
     * 数据库原始值
     */
    public function getOriginalValue()
    {
        return $this->originalValue;
    }

    /**
     * 数据库原始值
     */
    public function setOriginalValue($originalValue)
    {
        $this->originalValue = $originalValue;

        return $this;
    }

    /**
     * 当前表单中提交的值
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    public function disablePurifier($condition = true)
    {
        $this->disablePurifier = $condition;

        return $this;
    }

    /**
     * 当前表单中提交的值，不推荐在 Lego 外部调用，也就是不推荐在 Lego 外部修改.
     */
    public function setNewValue($value)
    {
        if ((!$this->disablePurifier) && $value && is_string($value)) {
            $value = $this->purifier->purify($value);
        }

        $this->newValue = $value;

        return $this;
    }

    /**
     * 获取只读模式下展示给用户的值
     */
    public function getDisplayValue()
    {
        return $this->displayValue;
    }

    /**
     * 设置只读模式下展示给用户的值
     */
    public function setDisplayValue($displayValue)
    {
        $this->displayValue = $displayValue;

        return $this;
    }

    /**
     * 当前 Field 的默认值
     */
    public function default($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * 用户输入时展示给用户的默认值
     */
    public function takeInputValue()
    {
        return $this->mutateTakingValue(
            lego_default(
                $this->getNewValue(),
                $this->getDefaultValue(),
                $this->getOriginalValue()
            )
        );
    }

    /**
     * 只读模式下展示给用户的值
     */
    public function takeShowValue()
    {
        return $this->mutateTakingValue(
            lego_default(
                $this->getDisplayValue(),
                $this->getDefaultValue(),
                $this->getOriginalValue()
            )
        );
    }

    /**
     * 数据读取时，将通过此函数进行必要变形.
     *
     * 例如：
     *  - Date 提交的数据一般为 Carbon 对象，展示时需要将其转换为特定 format
     *  - checkbox group 在数据库中存储的值为字符串，使用时需要将其转化为 array
     *
     * @return mixed
     */
    protected function mutateTakingValue($value)
    {
        return $value;
    }

    /**
     * Request 中提交的数据在存储前，将通过此函数进行必要的变形.
     *
     * 例如：
     *  checkbox group 选择多个值时，Request 中拿到的结果为 array，
     *  可在此函数中将 array 按需求转换为字符串等目标数据结构
     *
     * @return mixed
     */
    protected function mutateSavingValue($value)
    {
        return $value;
    }

    /**
     * 标记此字段不用存储到 Store ，即 Model.
     *
     * @param bool $condition
     *
     * @return $this
     */
    public function doesntStore($condition = true)
    {
        $this->doesntStore = $condition;

        return $this;
    }

    public function isDoesntStore()
    {
        return $this->doesntStore;
    }

    /**
     * 若输入值为空，存储时转换为 null.
     *
     * @var bool
     */
    protected $emptyToNull = false;

    public function emptyToNull()
    {
        $this->emptyToNull = true;

        return $this;
    }

    /**
     * 同步原始数据到 original value.
     */
    public function syncValueFromStore()
    {
        $this->setOriginalValue(
            $this->store->get($this->name())
        );
    }

    /**
     * 将新的数据存储到 Store.
     */
    public function syncValueToStore()
    {
        $value = $this->getNewValue();

        if ($this->emptyToNull && is_empty_string($value)) {
            $value = null;
        }

        $this->store->set($this->name(), $this->mutateSavingValue($value));
    }

    /**
     * 当前 Field 是否拥有有效输入值
     *
     * @return bool
     */
    public function hasValidNewValue()
    {
        $value = $this->getNewValue();

        if (is_null($value) || false === $value) {
            return false;
        }

        if (is_string($value)) {
            return !is_empty_string($value);
        }

        return true;
    }
}
