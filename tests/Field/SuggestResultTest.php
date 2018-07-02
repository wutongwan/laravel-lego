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

    public function testInputScalarArray()
    {
        $items = [
            'a',
            'b',
            'c',
        ];

        $expect = [
            'total_count' => 3,
            'items'       => [
                [
                    'value' => 'a',
                    'label' => 'a',
                ],
                [
                    'value' => 'b',
                    'label' => 'b',
                ],
                [
                    'value' => 'c',
                    'label' => 'c',
                ],
            ],
        ];

        $result = new SuggestResult($items);
        self::assertSame($expect, $result->toArray());
    }

    public function testInputKeyValueArray()
    {
        $items = [
            'a' => 'AA',
            'c' => 'CC',
        ];

        $expect = [
            'total_count' => 2,
            'items'       => [
                [
                    'value' => 'a',
                    'label' => 'AA',
                ],
                [
                    'value' => 'c',
                    'label' => 'CC',
                ],
            ],
        ];

        $result = new SuggestResult($items);
        self::assertSame($expect, $result->toArray());
    }

    public function testInputValueLabelArray()
    {
        $items = [
            ['value' => 'a', 'label' => 'AA'],
            ['value' => 'b', 'label' => 'BB'],
            ['value' => 'c', 'label' => 'CC'],
        ];

        $expect = [
            'total_count' => 3,
            'items'       => [
                [
                    'value' => 'a',
                    'label' => 'AA',
                ],
                [
                    'value' => 'b',
                    'label' => 'BB',
                ],
                [
                    'value' => 'c',
                    'label' => 'CC',
                ],
            ],
        ];

        $result = new SuggestResult($items);
        self::assertSame($expect, $result->toArray());
    }

    public function testInputIdTextArray()
    {
        $items = [
            ['id' => 'a', 'text' => 'AA'],
            ['id' => 'b', 'text' => 'BB'],
            ['id' => 'c', 'text' => 'CC'],
        ];

        $expect = [
            'total_count' => 3,
            'items'       => [
                [
                    'value' => 'a',
                    'label' => 'AA',
                ],
                [
                    'value' => 'b',
                    'label' => 'BB',
                ],
                [
                    'value' => 'c',
                    'label' => 'CC',
                ],
            ],
        ];

        $result = new SuggestResult($items);
        self::assertSame($expect, $result->toArray());
    }
}
