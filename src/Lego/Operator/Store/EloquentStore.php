<?php namespace Lego\Operator\Store;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Lego\Foundation\Exceptions\LegoSaveFail;

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

    protected $relations = [];

    public function getKeyName()
    {
        return $this->data->getKeyName();
    }

    /**
     * 获取属性值
     *
     * @param $attribute
     * @param null $default
     * @return mixed
     */
    public function get($attribute, $default = null)
    {
        return data_get($this->data, $attribute, $default);
    }

    /**
     * 修改属性值
     * @param $attribute
     * @param $value
     */
    public function set($attribute, $value)
    {
        $parts = explode('.', $attribute);

        if (count($parts) === 1) {
            $this->data->setAttribute($attribute, $value);
            return;
        }

        $relation = join('.', array_slice($parts, 0, -1));
        $related = data_get($this->data, $relation);
        if ($related && $related instanceof Model) {
            $related->setAttribute(last($parts), $value);
            $this->relations[$relation] = $related;
        }
    }

    /**
     * 存储操作
     *
     * 存储时尝试先存储 Relation ，再存储 Model ，任一失败则回滚
     *
     * @param array $options
     * @return bool
     * @throws LegoSaveFail
     */
    public function save($options = [])
    {
        foreach ($this->relations as $related) {
            if (!$related->save()) {
                $this->throwSaveError($related);
            }
        }

        if (!$this->data->save($options)) {
            $this->throwSaveError($this->data);
        }

        $this->data = $this->data->fresh();
        return true;
    }

    private function throwSaveError($data)
    {
        $class = class_basename($data);
        try {
            $dataString = json_encode($data, JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            $dataString = '[json encode fail]';
        }

        throw new LegoSaveFail($class . ' save fail, ' . $dataString);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data->toArray();
    }
}
