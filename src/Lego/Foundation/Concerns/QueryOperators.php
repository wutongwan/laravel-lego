<?php

namespace Lego\Foundation\Concerns;

interface QueryOperators
{
    const QUERY_EQ = '=';
    const QUERY_GT = '>';
    const QUERY_GTE = '>=';
    const QUERY_LT = '<';
    const QUERY_LTE = '<=';
    const QUERY_CONTAINS = 'contains';
    const QUERY_STARTS_WITH = 'starts_with';
    const QUERY_ENDS_WITH = 'ends_with';
}
