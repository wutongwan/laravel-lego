<?php

namespace Lego\Tests\Field;

use Illuminate\Support\Collection;
use Lego\Operator\SuggestResult;
use Lego\Tests\TestCase;

class SuggestResultTest extends TestCase
{
    public function testMain()
    {
        $items = [
            1 => 'code 1',
            2 => 'code 2',
        ];

        $expect = [
            'total_count' => 2,
            'items'       => [
                [
                    'value' => 1,
                    'label' => 'code 1',
                ],
                [
                    'value' => 2,
                    'label' => 'code 2',
                ],
            ],
        ];

        $result = new SuggestResult($items);
        self::assertEquals($expect, $result->toArray());

        $result = new SuggestResult(new Collection($items));
        self::assertEquals($expect, $result->toArray());
    }
}
