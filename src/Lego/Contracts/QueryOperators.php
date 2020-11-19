<?php

namespace Lego\Contracts;

interface QueryOperators
{
    const QUERY_EQ = '=';
    const QUERY_GT = '>';
    const QUERY_GTE = '>=';
    const QUERY_LT = '<';
    const QUERY_LTE = '<=';

    const QUERY_CONTAINS = 'Contains';
    const QUERY_STARTS_WITH = 'StartsWith';
    const QUERY_ENDS_WITH = 'EndsWith';
    const IN = 'In';
    const JSON_CONTAINS = 'JsonContains';
    const BETWEEN = 'Between';

    const ALL = [
        self::QUERY_EQ,
        self::QUERY_GT,
        self::QUERY_GTE,
        self::QUERY_LT,
        self::QUERY_LTE,
        self::QUERY_CONTAINS,
        self::QUERY_STARTS_WITH,
        self::QUERY_ENDS_WITH,

        self::IN,
        self::JSON_CONTAINS,
        self::BETWEEN,
    ];
}
