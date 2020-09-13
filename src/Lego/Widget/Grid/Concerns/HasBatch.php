<?php

namespace Lego\Widget\Grid\Concerns;

use Lego\Operator\Store;
use Lego\Widget\Grid\Batch;

trait HasBatch
{
    /**
     * @var Batch[]
     */
    protected $batches = [];
    protected $batchIdName = 'id';

    public function addBatch($name, \Closure $action = null, $primaryKey = null)
    {
        $batch = new Batch($name, $this->getQuery(), $primaryKey ?: $this->batchIdName);
        $this->batches[$name] = $batch;

        if ($action) {
            $batch->action($action);
        }

        return $batch;
    }

    public function batches()
    {
        return $this->batches;
    }

    public function batch($name)
    {
        return $this->batches[$name];
    }

    /**
     * 设置每条数据的标记字段名.
     *
     * @param string $keyName
     *
     * @return $this
     */
    public function setBatchIdName(string $keyName)
    {
        $this->batchIdName = $keyName;

        return $this;
    }

    /**
     * 每条数据的标记字段名.
     *
     * @return string
     */
    public function getBatchIdName()
    {
        return $this->batchIdName;
    }

    /**
     * 获取批处理 id 列表.
     *
     * @return array
     */
    public function pluckBatchIds(): array
    {
        $ids = [];

        /** @var Store $store */
        foreach ($this->paginator() as $store) {
            $ids[] = $store->get($this->batchIdName);
        }

        return $ids;
    }
}
