<?php namespace Lego\Operator;

use Lego\Foundation\Exceptions\LegoException;

class Finder
{
    protected $operators = [
        Query::class => [
            Eloquent\EloquentQuery::class,
            Collection\ArrayQuery::class,
        ],

        Store::class => [
            Eloquent\EloquentStore::class,
            Collection\ArrayStore::class,
            Collection\ObjectStore::class,
        ],
    ];

    public function __construct()
    {
        // test plastic is installed
        if (class_exists(\Sleimanx2\Plastic\Facades\Plastic::class)) {
            $this->operators[Query::class][] = Plastic\PlasticQuery::class;
        }
    }

    public function parse($operatorType, $data)
    {
        if ($data instanceof $operatorType) {
            return $data;
        }

        /** @var Operator|\Lego\Operator\Store|\Lego\Operator\Query $item */
        foreach ($this->operators[$operatorType] as $item) {
            if ($operator = $item::parse($data)) {
                return $operator;
            }
        }

        throw new LegoException("Cannot create {$operatorType} form \$data");
    }

    /**
     * @var self
     */
    protected static $instance;

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    /**
     * Query Operator finder
     *
     * @param $data
     * @return \Lego\Operator\Query
     * @throws LegoException
     */
    public static function createQuery($data)
    {
        return static::instance()->parse(\Lego\Operator\Query::class, $data);
    }

    /**
     * Store Operator finder
     *
     * @param $data
     * @return \Lego\Operator\Store
     * @throws LegoException
     */
    public static function createStore($data)
    {
        return static::instance()->parse(\Lego\Operator\Store::class, $data);
    }
}
