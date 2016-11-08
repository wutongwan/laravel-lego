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
     * 存储操作
     *
     * 存储时尝试先存储 Relation ，再存储 Model ，任一失败则回滚
     *
     * @param array $options
     * @return bool
     */
    public function save($options = []): bool
    {
        \DB::beginTransaction();

        try {
            $failed = $this->saveRelations() === false || $this->original->save() === false;
            if ($failed) {
                \DB::rollBack();
                return false;
            }
            \DB::commit();
            return true;
        } catch (\Throwable $e) {
            \DB::rollBack();
            return false;
        }
    }

    private function saveRelations()
    {
        /** @var Eloquent $related */
        foreach ($this->original->getRelations() as $related) {
            if ($related->isDirty()) {
                if ($related->save() === false) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 当前对象自带的 Validation
     * 参照: https://laravel.com/docs/5.2/validation
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}