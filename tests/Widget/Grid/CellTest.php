<?php namespace Lego\Tests\Widget\Grid;

use Carbon\Carbon;
use Lego\Widget\Grid\Cell;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    public function testValue()
    {
        $cell = new Cell('name', 'Name');
        $cell->fill(['name' => 'zhwei']);
        $this->assertEquals('zhwei', $cell->value());
    }

    public function testDefault()
    {
        $cell = new Cell('name', 'Name');
        $cell->default('default')->fill([]);
        $this->assertEquals('default', $cell->value());
    }

    /**
     * @dataProvider commonPipesDataProvider
     */
    public function testCommonPipes($pipe, $input, $output)
    {
        $cell = (new Cell('key', 'key'))
            ->fill(['key' => $input])
            ->pipe($pipe);

        $this->assertEquals($output, $cell->value()->toHtml());
    }

    public function commonPipesDataProvider()
    {
        return [
            ['trim', ' 123 ', '123'],
            ['trim', ' 123', '123'],
            ['date', 1488902400, '2017-03-08'],
            ['date', '2017-03-08', '2017-03-08'],
            ['date', '2017-03-08 12:00:00', '2017-03-08'],
            ['date', Carbon::now(), Carbon::today()->format('Y-m-d')],

            ['datetime', 1488902400, '2017-03-08 00:00:00'],
            ['datetime', '2017-03-08', '2017-03-08 00:00:00'],
            ['datetime', '2017-03-08 12:00:00', '2017-03-08 12:00:00'],
            ['datetime', Carbon::now(), Carbon::now()->format('Y-m-d H:i:s')],
            ['datetime', 'now', Carbon::now()->format('Y-m-d H:i:s')],

            ['time', 1488902401, '00:00:01'],
            ['time', '2017-03-08', '00:00:00'],
            ['time', '2017-03-08 12:00:00', '12:00:00'],
            ['time', Carbon::now(), Carbon::now()->format('H:i:s')],
            ['time', 'now', Carbon::now()->format('H:i:s')],
        ];
    }

    public function testMultiPipe()
    {
        $cell = (new Cell('key', 'key'))
            ->fill(['key' => ' 1 '])
            ->pipe('trim')
            ->pipe(function ($value) {
                $this->assertEquals('1', $value);
                return $value * 2;
            })
            ->pipe(function ($value) {
                $this->assertEquals(2, $value);
                return $value + 19;
            })
        ;

        $this->assertEquals(21, $cell->value()->toHtml());
    }
}
