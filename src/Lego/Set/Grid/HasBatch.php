<?php

namespace Lego\Set\Grid;

use Lego\Contracts\QueryOperators;
use Lego\Foundation\FieldName;

trait HasBatch
{
    /**
     * @var array<string, Batch>
     */
    private $batches = [];

    public function addBatch(string $name): Batch
    {
        return $this->batches[$name] = $this->container->make(Batch::class, [
            'name' => $name,
            'rowsRetriever' => function (array $ids) {
                $name = new FieldName($this->getBatchKeyName());
                $adaptor = $this->getAdaptor();
                $adaptor->where($name, QueryOperators::IN, $ids);
                return $adaptor->get();
            },
        ]);
    }

    /**
     * @return array<string, Batch>
     */
    public function getBatches(): array
    {
        return $this->batches;
    }

    /**
     * 批处理中使用的主键字段名，默认使用 model 的 getKeyName()
     *
     * @var string
     */
    private $batchKeyName;

    /**
     * 设定批处理使用的主键字段名称
     * @param string $batchKeyName
     * @return $this
     */
    public function setBatchKeyName(string $batchKeyName)
    {
        $this->batchKeyName = $batchKeyName;
        return $this;
    }

    private function getBatchKeyName()
    {
        return $this->batchKeyName ?: $this->getAdaptor()->getKeyName();
    }
}
