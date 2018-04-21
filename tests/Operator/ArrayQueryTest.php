<?php namespace Lego\Tests\Operator;

use Carbon\Carbon;
use Lego\Operator\Collection\ArrayQuery;
use Lego\Operator\Store;
use Lego\Tests\TestCase;

class ArrayQueryTest extends TestCase
{
    public function testWhere()
    {
        $array = [
            ['a' => 'a_value', 'integer' => 1],
            ['a' => 'a_value', 'integer' => 2],
            ['a' => 'a_diff_value', 'integer' => 3],
            ['a' => 'a_diff_value', 'date' => new Carbon('2018-04-01')],
        ];

        $result = ArrayQuery::parse($array)->whereContains('a', 'diff')->get();
        self::assertCount(2, $result);
        foreach ($result as $item) {
            self::assertInstanceOf(Store::class, $item);
            self::assertSame('a_diff_value', $item->get('a'));
        }

        $result = ArrayQuery::parse($array)->whereGt('date', new Carbon('20180331'))->get();
        self::assertCount(1, $result);
        self::assertEquals(new Carbon('20180401'), $result->first()->get('date'));
    }

    public function testSuggest()
    {
        $array = [
            ['a' => 'a'],
            ['a' => 'abc'],
            ['a' => 'bcd'],
            ['a' => 'end_a'],
        ];

        $result = ArrayQuery::parse($array)->suggest('a', 'a');

        self::assertSame($result->toArray(), [
            'total_count' => 3,
            'items' => [
                ["value" => "a", "label" => "a"],
                ["value" => "abc", "label" => "abc"],
                ["value" => "end_a", "label" => "end_a"],
            ],
        ]);
    }
}
