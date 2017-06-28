<?php namespace Lego\Tests\Widget\Grid;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Lego\Tests\TestCase;
use Lego\Widget\Grid\Cell;
use Lego\Widget\Grid\PipeHandler;
use Lego\Widget\Grid\Pipes4Datetime;
use Lego\Widget\Grid\Pipes4String;

class PipeTest extends TestCase
{
    public function testAbc()
    {
        $cell = new Cell('key|trim|date', 'Key');
        $this->assertEquals('key', $cell->name());

        $pipes = (new \ReflectionClass(Cell::class))->getProperty('pipes');
        $pipes->setAccessible(true);

        $pipeClass = (new \ReflectionClass(PipeHandler::class))->getProperty('pipeClass');
        $pipeClass->setAccessible(true);
        $this->assertEquals(
            [Pipes4String::class, Pipes4Datetime::class],
            array_map(
                function ($handler) use ($pipeClass) {
                    return $pipeClass->getValue($handler);
                },
                $pipes->getValue($cell)
            )
        );
    }

    /**
     * @dataProvider builtInPipesDataProvider
     */
    public function testBuiltInPipes($pipe, $input, $output)
    {
        $cell = (new Cell('key', 'key'))
            ->fill(['key' => $input])
            ->pipe($pipe);

        $this->assertEquals($output, $cell->value()->toHtml());
    }

    public function builtInPipesDataProvider()
    {
        Carbon::setTestNow('2017-03-08 01:02:03');
        return [
            ['trim', ' 123 ', '123'],
            ['trim', ' 123', '123'],

            ['date', strtotime('2017-03-08'), '2017-03-08'],
            ['date', '2017-03-08', '2017-03-08'],
            ['date', '2017-03-08 12:00:00', '2017-03-08'],
            ['date', Carbon::now(), Carbon::today()->format('Y-m-d')],

            ['datetime', strtotime('2017-03-08'), '2017-03-08 00:00:00'],
            ['datetime', '2017-03-08', '2017-03-08 00:00:00'],
            ['datetime', '2017-03-08 12:00:00', '2017-03-08 12:00:00'],
            ['datetime', Carbon::now(), Carbon::now()->format('Y-m-d H:i:s')],

            ['time', strtotime('2017-03-08 00:00:01'), '00:00:01'],
            ['time', '2017-03-08', '00:00:00'],
            ['time', '2017-03-08 12:00:00', '12:00:00'],
            ['time', Carbon::now(), Carbon::now()->format('H:i:s')],
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
            });

        $this->assertEquals(21, $cell->value()->toHtml());
    }

    public function testPipeWithArgs()
    {
        $cell = new Cell('key|date-format:YmdHis', 'key');
        $cell->fill(['key' => $time = Carbon::now()]);
        $this->assertEquals($time->format('YmdHis'), $cell->getPlainValue());
    }

    /**
     * @dataProvider userDefinedPipeProvider
     */
    public function testUserDefinedPipe($pipe, $expected)
    {
        $key = 'lego.widgets.grid.pipes';
        Config::set($key, array_merge(Config::get($key), [ExamplePipes::class]));

        $cell = new Cell('abc|' . $pipe, 'ABC');
        $cell->fill(['abc' => 1, 'code' => '007']);
        $this->assertEquals($expected, $cell->getPlainValue());
    }

    public function userDefinedPipeProvider()
    {
        return [
            ['always-hello-lego', 'hello lego'],
            ['always:lego', 'lego'],
            ['increment', 2],
            ['increment-by:7', 8],
            ['return-attribute-code', '007']
        ];
    }

    protected function tearDown()
    {
        parent::tearDown();

        PipeHandler::forgetRegistered();
    }
}
