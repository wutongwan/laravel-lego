<?php namespace Lego\Data\Row;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class EloquentRow
 * @package Lego\Data\Row
 * @mixin Eloquent
 */
class EloquentRow extends Row
{
    /**
     * @var Eloquent $original
     */
    protected $original;

    /**
     * 获取属性值
     *
     * @param $attribute
     * @param null $default
     * @return mixed
     */
    public function get($attribute, $default = null)
    {
        return object_get($this->original, $attribute, $default);
    }

    /**
     * 修改属性值
     * @param $attribute
     * @param $value
     */
    public function set($attribute, $value)
    {
        data_set($this->original, $attribute, $value);
    }

    /**
     * 存储操作, 针对 Eloquent 等场景
     * @param array $options
     * @return mixed
     */
    public function save($options = [])
    {
        return $this->original->save($options);
    }

    /**
     * 当前对象自带的 Validation
     * 参照: https://laravel.com/docs/5.2/validation
     * @return array
     */
    public function rules() : array
    {
        return [];
    }
}