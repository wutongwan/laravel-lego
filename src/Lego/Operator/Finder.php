<?php namespace Lego\Operator;

use Illuminate\Support\Facades\Config;
use Lego\Foundation\Exceptions\LegoException;

class Finder
{
    protected $operators = [
        Query::class => [
            100 => Eloquent\EloquentQuery::class,
            200 => Outgoing\OutgoingQuery::class,
            300 => Collection\ArrayQuery::class,
        ],

        Store::class => [
            100 => Eloquent\EloquentStore::class,
            200 => Outgoing\OutgoingStore::class,
            300 => Collection\ArrayStore::class,
            400 => Collection\ObjectStore::class,
        ],
    ];

    public function __construct()
    {
        // test plastic is installed
        if (class_exists(\Sleimanx2\Plastic\Facades\Plastic::class)) {
            $this->operators[Query::class][110] = Plastic\PlasticQuery::class;
        }

        $this->operators = array_merge_recursive(
            $this->operators,
            Config::get('lego.operators', [])
        );
    }

    public function parse($operatorType, $data)
    {
        if ($data instanceof $operatorType) {
            return $data;
        }

        $operators = array_filter($this->operators[$operatorType]);
        ksort($operators);

        /** @var Operator|\Lego\Operator\Store|\Lego\Operator\Query $item */
        foreach ($operators as $item) {
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
     * OutgoingQuery Operator finder
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
