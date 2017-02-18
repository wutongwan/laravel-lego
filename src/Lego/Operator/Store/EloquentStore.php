<?php namespace Lego\Operator\Store;

use Illuminate\Database\Eloquent\Model;

class EloquentStore extends Store
{
    public static function attempt($data)
    {
        if ($data instanceof Model) {
            return new self($data);
        }

        if (is_string($data) && is_subclass_of($data, Model::class)) {
            return new self(new $data);
        }

        return false;
    }

    /** @var Model $data */
    protected $data;

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
    public function save($options = [])
    {
        return $this->data->saveOrFail();
    }
}
