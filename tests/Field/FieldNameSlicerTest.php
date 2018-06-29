<?php

namespace Lego\Tests\Field;

use Lego\Field\FieldNameSlicer;
use Lego\Tests\TestCase;

class FieldNameSlicerTest extends TestCase
{
    public function testSplit()
    {
        self::assertSame([[], 'name', []], FieldNameSlicer::split('name'));

        self::assertSame([[], 'name', ['json_key']], FieldNameSlicer::split('name:json_key'));

        self::assertSame([['a', 'b'], 'name', []], FieldNameSlicer::split('a.b.name'));

        self::assertSame(
            [['school', 'city'], 'column', ['json_key', 'sub_json_key']],
            FieldNameSlicer::split('school.city.column:json_key:sub_json_key')
        );
    }
}
