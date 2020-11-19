<?php

namespace Lego\Tests\Utility;

use Lego\Utility\JsonUtility;
use PHPUnit\Framework\TestCase;
use stdClass;

class JsonUtilityTest extends TestCase
{
    public function testString()
    {
        self::assertSame(null, JsonUtility::get('', 'hello.world'));
        self::assertSame('c', JsonUtility::get('{"a":{"b":"c"}}', 'a.b'));

        self::assertSame('{"a":{"b":"c"}}', JsonUtility::set('', 'a.b', 'c'));
        self::assertSame('{"a":{"b":{"e":"f"}}}', JsonUtility::set('', 'a.b', ['e' => 'f']));
    }

    public function testArray()
    {
        self::assertSame(null, JsonUtility::get(null, 'hello.world'));
        self::assertSame(null, JsonUtility::get([], 'hello.world'));
        self::assertSame('c', JsonUtility::get(['a' => ['b' => 'c']], 'a.b'));

        self::assertSame(['a' => ['b' => 'c']], JsonUtility::set(['a' => []], 'a.b', 'c'));
        self::assertSame(['a' => ['b' => ['e' => 'f']]], JsonUtility::set(['a' => []], 'a.b', ['e' => 'f']));
    }

    public function testObject()
    {
        self::assertSame(null, JsonUtility::get(new stdClass(), 'hello.world'));

        $o = new stdClass();
        $o->a = new stdClass();
        $o->a->b = 'c';
        self::assertSame('c', JsonUtility::get($o, 'a.b'));

        $o = new stdClass();
        self::assertSame(
            '{"a":{"b":{"e":"f"}}}',
            json_encode(JsonUtility::set($o, 'a.b', ['e' => 'f']))
        );
    }
}
