<?php namespace Lego\Source\Record;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class EloquentRecord
 * @package Lego\Source\Item
 * @mixin Eloquent
 */
class EloquentRecord extends Record
{
    /**
     * @var Eloquent $record
     */
    protected $record;

    /**
     * 获取属性值
     *
     * @param $attribute
     * @param null $default
     * @return mixed
     */
    public function get($attribute, $default = null)
    {
        return $this->record->getAttribute($attribute, $default);
    }

    /**
     * 修改属性值
     * @param $attribute
     * @param $value
     */
    public function set($attribute, $value)
    {
        $this->record->{$attribute} = $value;
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