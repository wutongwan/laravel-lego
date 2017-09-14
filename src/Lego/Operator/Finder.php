<?php namespace Lego\Operator;

use Lego\Foundation\Exceptions\LegoException;

class Finder
{
    protected $operators = [
        Query\Query::class => [
            Query\EloquentQuery::class,
            Query\ArrayQuery::class,
        ],

        Store\Store::class => [
            Store\EloquentStore::class,
            Store\ArrayStore::class,
            Store\ObjectStore::class,
        ],
    ];

    public function __construct()
    {
        // test plastic is installed
        if (class_exists(\Sleimanx2\Plastic\Facades\Plastic::class)) {
            $this->operators[Query\Query::class][] = Query\PlasticQuery::class;
        }
    }

    public function parse($operatorType, $data)
    {
        if ($data instanceof $operatorType) {
            return $data;
        }

        /** @var Operator|Store\Store|Query\Query $item */
        foreach ($this->operators[$operatorType] as $item) {
            if ($operator = $item::attempt($data)) {
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
     * @return Query\Query
     * @throws LegoException
     */
    public static function query($data)
    {
        return static::instance()->parse(Query\Query::class, $data);
    }

    /**
     * Store Operator finder
     *
     * @param $data
     * @return Store\Store
     * @throws LegoException
     */
    public static function store($data)
    {
        return static::instance()->parse(Store\Store::class, $data);
    }
}
