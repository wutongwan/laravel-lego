<?php

namespace Lego\Operator\Elastic;

use Elasticsearch\ClientBuilder;

class ElasticClient
{
    /**
     * @var array
     */
    protected $hosts;

    /**
     * @var string
     */
    protected $index;

    /**
     * @var string|null
     */
    protected $type;

    public function __construct(array $hosts, string $index, string $type = null)
    {
        $this->hosts = $hosts;
        $this->index = $index;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getIndex(): string
    {
        return $this->index;
    }

    /**
     * @param string $index
     */
    public function setIndex(string $index)
    {
        $this->index = $index;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    protected $connection;

    public function connection()
    {
        if (!$this->connection) {
            $this->connection = ClientBuilder::fromConfig([
                'hosts' => $this->hosts
            ]);
        }
        return $this->connection;
    }
}
