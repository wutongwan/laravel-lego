<?php namespace Lego\Operator;

use Lego\Foundation\Exceptions\LegoException;

class Finder
{
    const QUERY_LIST = [
        Query\EloquentQuery::class,
    ];

    const STORE_LIST = [
        Store\EloquentStore::class,
    ];

    /**
     * Query Operator finder
     *
     * @param $data
     * @return Query\Query
     * @throws LegoException
     */
    public static function query($data)
    {
        if ($data instanceof Query\Query) {
            return $data;
        }

        foreach (self::QUERY_LIST as $item) {
            if ($query = $item::attempt($data)) {
                return $query;
            }
        }

        throw new LegoException('Cannot create query operator form $data');
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
        if ($data instanceof Store\Store) {
            return $data;
        }

        foreach (self::STORE_LIST as $item) {
            if ($store = $item::attempt($item)) {
                return $store;
            }
        }

        throw new LegoException('Cannot create store operator form $data');
    }
}
