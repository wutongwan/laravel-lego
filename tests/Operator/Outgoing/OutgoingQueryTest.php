<?php

namespace Lego\Tests\Operator\Outgoing;

use Lego\Lego;
use Lego\Operator\Outgoing\OutgoingInterface;
use Lego\Operator\Outgoing\OutgoingQuery;
use Lego\Tests\TestCase;

class OutgoingQueryTest extends TestCase
{
    public function testParse()
    {
        $q = OutgoingQuery::parse(OutgoingInterface::class);
        self::assertInstanceOf(OutgoingQuery::class, $q);

        $q = OutgoingQuery::parse(ExampleOutgoing::class);
        self::assertInstanceOf(OutgoingQuery::class, $q);

        $a = new ExampleOutgoing();
        $q = OutgoingQuery::parse($a);
        self::assertInstanceOf(OutgoingQuery::class, $q);

        $q = OutgoingQuery::parse(new \stdClass());
        self::assertFalse($q);

        $f = Lego::outgoingFilter();
        self::assertInstanceOf(OutgoingQuery::class, $f->getQuery());
    }

    public function testOutput()
    {
        $q = OutgoingQuery::parse(ExampleOutgoing::class);
        $q->whereEquals('equals_field', 'equals value');
        $q->whereGt('gt_field', 'gt_value');
        $q->whereGte('gte_field', 'gte_value');
        $q->whereLt('lte_field', 'lte_value');
        $q->whereLte('lte_field', 'lte_value');
        $q->whereIn('in_field', ['in_value1', 'in_value2']);
        $q->whereBetween('btw_field', 'btw_min_value', 'btw_max_value');
        $q->whereScope('scope_field', 'scope_value');
        $q->whereContains('contains_field', 'contains_value');
        $q->whereStartsWith('starts_with_field', 'starts_with_value');
        $q->whereEndsWith('ends_with_field', 'ends_with_value');
        $q->orderBy('order_asc');
        $q->orderBy('order_desc', true);
        $q->limit(67);

        $expected = [
            'wheres' => [
                [
                    'attribute' => 'equals_field',
                    'operator'  => '=',
                    'value'     => 'equals value',
                ],
                [
                    'attribute' => 'gt_field',
                    'operator'  => '>',
                    'value'     => 'gt_value',
                ],
                [
                    'attribute' => 'gte_field',
                    'operator'  => '>=',
                    'value'     => 'gte_value',
                ],
                [
                    'attribute' => 'lte_field',
                    'operator'  => '<',
                    'value'     => 'lte_value',
                ],
                [
                    'attribute' => 'lte_field',
                    'operator'  => '<=',
                    'value'     => 'lte_value',
                ],
                [
                    'attribute' => 'in_field',
                    'operator'  => 'in',
                    'value'     => [
                        'in_value1',
                        'in_value2',
                    ],
                ],
                [
                    'attribute' => 'btw_field',
                    'operator'  => 'between',
                    'value'     => [
                        'btw_min_value',
                        'btw_max_value',
                    ],
                ],
                [
                    'attribute' => 'scope_field',
                    'operator'  => 'scope',
                    'value'     => 'scope_value',
                ],
                [
                    'attribute' => 'contains_field',
                    'operator'  => 'contains',
                    'value'     => 'contains_value',
                ],
                [
                    'attribute' => 'starts_with_field',
                    'operator'  => 'contains:starts_with',
                    'value'     => 'starts_with_value',
                ],
                [
                    'attribute' => 'ends_with_field',
                    'operator'  => 'contains:ends_with',
                    'value'     => 'ends_with_value',
                ],
            ],
            'orders' => [
                ['order_asc', 'asc'],
                ['order_desc', 'desc'],
            ],
            'limit'      => 67,
            'pagination' => [],
        ];
        self::assertEquals($expected, $q->getConditions());
        self::assertEquals($expected, $q->toArray());
    }

    public function testPagination()
    {
        $f = Lego::outgoingFilter();
        self::assertFalse($f->isPaginatorEnabled());

        $f->paginate(666);
        self::assertEquals([
            'perPage'     => 666,
            'pageName'    => 'page',
            'page'        => 1,
            'lengthAware' => true,
        ], $f->getResult()['pagination']);
        self::assertTrue($f->isPaginatorEnabled());

        $f = Lego::outgoingFilter();
        $f->simplePaginate(777);
        self::assertEquals([
            'perPage'     => 777,
            'pageName'    => 'page',
            'page'        => 1,
            'lengthAware' => false,
        ], $f->getResult()['pagination']);
    }
}

class ExampleOutgoing implements OutgoingInterface
{
}
