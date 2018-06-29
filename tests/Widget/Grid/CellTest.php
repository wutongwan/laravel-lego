<?php

namespace Lego\Tests\Widget\Grid;

use Illuminate\Support\HtmlString;
use Lego\Widget\Grid\Cell;

class CellTest extends \Lego\Tests\TestCase
{
    public function testAbc()
    {
        $cell = (new Cell('name', 'Your Name'));

        // meta
        $this->assertEquals('name', $cell->name());
        $this->assertEquals('Your Name', $cell->description());

        // values
        $cell = $cell->fill(['name' => 'zhwei']);

        $this->assertInstanceOf(HtmlString::class, $cell->value());
        $this->assertEquals('zhwei', $cell->value());

        $this->assertTrue(is_string($cell->getPlainValue()));
        $this->assertEquals('zhwei', $cell->getPlainValue());

        $this->assertTrue(is_string($cell->getOriginalValue()));
        $this->assertEquals('zhwei', $cell->getOriginalValue());
    }

    public function testDefault()
    {
        $cell = (new Cell('name', 'Name'))->default('default')->fill([]);
        $this->assertEquals('default', $cell->value());
    }
}
