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

    public function addBatch(string $name, string $keyName = null): Batch
    {
        return $this->batches[$name] = $this->container->make(Batch::class, [
            'name' => $name,
            'rowsRetriever' => function (array $ids) use ($keyName) {
                $keyName = $keyName ?: $this->getAdaptor()->getKeyName();
                $adaptor = $this->getAdaptor();
                $adaptor->where(new FieldName($keyName), QueryOperators::IN, $ids);
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
}
