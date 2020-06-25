<?php

namespace Lego\Tests\Widget\Grid;

use Lego\Operator\Collection\ArrayStore;
use Lego\Widget\Grid\FormatTool;
use PHPUnit\Framework\TestCase;

class FormatToolTest extends TestCase
{
    public function testFormat()
    {
        $store = new ArrayStore([
            'hello' => 'world',
            'cat' => [
                'name' => 'Tom',
                'friends' => [
                    'first' => 'Jerry',
                ],
            ]
        ]);

        $actual = FormatTool::format(
            'zoo',
            'this is {}, cat name is {cat.name}, first friend is {cat.friends.first}, '
            . 'second friend is {cat.friends.second}',
            $store
        );

        self::assertSame(
            'this is zoo, cat name is Tom, first friend is Jerry, second friend is ',
            $actual
        );
    }
}
