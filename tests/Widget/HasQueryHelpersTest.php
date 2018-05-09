<?php namespace Lego\Tests\Widget;

use Lego\Lego;
use Lego\Tests\TestCase;

class HasQueryHelpersTest extends TestCase
{
    public function testMain()
    {
        $filter = Lego::outgoingFilter();
        $filter->limit(67);

        $filter->orderBy('asc_column');
        $filter->orderByDesc('desc_column');

        self::assertSame(
            [
                "wheres" => [],
                "orders" => [
                    ["asc_column", "asc"],
                    ["desc_column", "desc"],
                ],
                "limit" => 67,
            ],
            $filter->getQuery()->toArray()
        );
    }
}
