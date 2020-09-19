<?php

namespace Lego\Tests\Foundation;

use Lego\Foundation\FieldName;
use PHPUnit\Framework\TestCase;

class FieldNameTest extends TestCase
{
    public function testFullFeatures()
    {
        $fn = new FieldName($original = 'country.city.json_column$.jsonKey.jsonSubKey|format:Y-m-d,Asia/Shanghai|limit:100');
        self::assertSame('json_column', $fn->getColumnName());
        self::assertSame('country.city', $fn->getRelation());
        self::assertSame('country.city.json_column', $fn->getQualifiedColumnName());
        self::assertSame('jsonKey.jsonSubKey', $fn->getJsonPath());
        self::assertSame($original, $fn->getOriginal());
        self::assertSame([
            ['name' => 'format', 'args' => ['Y-m-d', 'Asia/Shanghai']],
            ['name' => 'limit', 'args' => ['100']],
        ], $fn->getPipelines());
    }

    public function testDefaults()
    {
        $fn = new FieldName($original = 'hello');
        self::assertSame($original, $fn->getColumnName());
        self::assertSame('', $fn->getRelation());
        self::assertSame($original, $fn->getQualifiedColumnName());
        self::assertSame('', $fn->getJsonPath());
        self::assertSame($original, $fn->getOriginal());
        self::assertSame([], $fn->getPipelines());
    }

    public function testCommons()
    {
        $fn = new FieldName('hello.world');
        self::assertSame('hello', $fn->getRelation());
        self::assertSame('hello.world', $fn->getQualifiedColumnName());
    }

    public function testInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        new FieldName($original = 'hello.$.||||');
    }

    public function testInvalidJsonPath()
    {
        $this->expectException(\InvalidArgumentException::class);
        new FieldName($original = 'hello.$.[0]||||');
    }

    public function testValidJsonPath()
    {
        $fn = new FieldName($original = 'hello$.world');
        self::assertSame('world', $fn->getJsonPath());
    }
}
