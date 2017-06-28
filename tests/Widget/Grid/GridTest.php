<?php namespace Lego\Tests\Widget\Grid;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Lego\Register\HighPriorityResponse;
use Lego\Tests\TestCase;
use Lego\Tests\Tools\FakeMobileDetect;
use Lego\Widget\Filter;
use Lego\Widget\Grid\Cell;
use Lego\Widget\Grid\Grid;

class GridTest extends TestCase
{
    public function testAbc()
    {
        $text = 'zhwei\'s lego test';
        $grid = new Grid([['a' => $text]]);
        $this->assertNotContains($text, $this->render2html($grid));

        $grid->add('a', 'A Text');
        $html = $this->render2html($grid);
        $this->assertContains($text, $html);
        $this->assertContains($text, $html);
    }

    public function testPagination()
    {
        // no paginator
        $grid = $this->fakeGrid();
        $grid->paginate(100);
        $html = $this->render2html($grid);
        $this->assertNotContains('pagination', $html);
        $this->assertNotContains('?page=2', $html);

        // paginator
        $grid = $this->fakeGrid();
        $grid->paginate(7);
        $html = $this->render2html($grid);
        $this->assertContains('pagination', $html);
        $this->assertContains('page=4', $html);
        $this->assertNotContains('page=5', $html);

        // change pager name
        $grid = $this->fakeGrid();
        $grid->paginate(7, 'lego-test-pager');
        $this->assertContains('/?lego-test-pager=2', $this->render2html($grid));
    }

    private function fakeGrid($length = 24)
    {
        $grid = new Grid($this->fakeDataArray($length));
        $grid->add('user', 'User Name');
        $grid->add('city', 'City');
        $grid->add('address', 'Address');
        return $grid;
    }

    private function fakeDataArray($length)
    {
        $array = [];
        $faker = $this->faker();
        for ($i = 0; $i < $length; $i++) {
            $array[] = [
                'user' => $faker->userName,
                'city' => $faker->city,
                'address' => $faker->address,
            ];
        }
        return $array;
    }

    public function testResponsive()
    {
        $grid = $this->fakeGrid(1);
        $this->assertContains($this->pcGridHeader($grid), $this->render2html($grid));

        // set to mobile mode
        FakeMobileDetect::mockIsMobile();

        // test disable responsive globally
        Config::set('lego.widgets.grid.responsive', false);
        $grid = $this->fakeGrid(1);
        $this->assertContains($this->pcGridHeader($grid), $this->render2html($grid));

        $grid->responsive();
        $this->assertNotContains($this->pcGridHeader($grid), $this->render2html($grid));
        $this->assertContains('<div id="' . $grid->uniqueId() . '">', $this->render2html($grid));
        $this->assertContains('<ul class="list-group">', $this->render2html($grid));
    }

    private function pcGridHeader(Grid $grid)
    {
        return '<table class="table" id="' . $grid->uniqueId() . '">';
    }

    public function testOrderBy()
    {
        $this->assertAfter(
            $this->render2html($this->createOrderByTestGrid()), 'first-line', 'second-line'
        );

        $grid = $this->createOrderByTestGrid();
        $grid->orderBy('a');
        $this->assertAfter($this->render2html($grid), 'first-line', 'second-line');

        $grid = $this->createOrderByTestGrid();
        $grid->orderBy('a', 'desc');
        $this->assertAfter($this->render2html($grid), 'second-line', 'first-line');

        $grid = $this->createOrderByTestGrid();
        $grid->orderByDesc('a');
        $this->assertAfter($this->render2html($grid), 'second-line', 'first-line');
    }

    private function createOrderByTestGrid()
    {
        $grid = new Grid([
            ['a' => 1, 'id' => 'first-line'],
            ['a' => 2, 'id' => 'second-line'],
        ]);
        $grid->add('a', 'A');
        $grid->add('id', 'ID');
        return $grid;
    }

    private function assertAfter($subject, $before, $after)
    {
        $this->assertContains($before, $subject);
        $this->assertContains($after, $subject);

        $this->assertTrue(strpos($subject, $before) < strpos($subject, $after), 'location wrong');
    }

    public function testCreateFromFilter()
    {
        $data = $this->fakeDataArray(10);
        $filter = new Filter($data);
        $grid = new Grid($filter);
        $grid->add('user', 'User Name');
        $html = $this->render2html($grid);
        foreach ($data as $datum) {
            $this->assertContains($datum['user'], $html);
        }
    }

    public function testExport()
    {
        $grid = $this->fakeGrid();
        $grid->export('Test Export', function (Grid $grid) {
            $grid->remove('address');
        });
        $this->assertContains('Test Export', $this->render2html($grid));

        $data = $grid->data();
        /** @var \Maatwebsite\Excel\Classes\PHPExcel $e */
        $e = $grid->exportAsExcel('example')->getExcel();
        foreach ($e->getSheet()->toArray() as $idx => $cell) {
            if ($idx === 0) {
                $this->assertEquals(['User Name', 'City', 'Address'], $cell);
            } else {
                $this->assertEquals(array_values($data[$idx - 1]), $cell);
            }
        }

        $this->assertStringStartsWith('http://', $grid->exports()['Test Export']);
        $this->assertContains(HighPriorityResponse::REQUEST_PARAM, $grid->exports()['Test Export']);
    }

    public function testGetResult()
    {
        $pipe = function ($user) {
            return "<strong>{$user}</strong>";
        };

        $grid = $this->fakeGrid();
        $grid->cell('user')->pipe($pipe);
        $result = $grid->getResult();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(
            collect($grid->data())->pluck('user')->map($pipe)->toArray(),
            $result->values()->pluck('User Name')->toArray()
        );

        $result = $grid->getPlainResult();
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(
            collect($grid->data())->pluck('user')->toArray(),
            $result->values()->pluck('User Name')->toArray()
        );
    }

    public function testCells()
    {
        $grid = new Grid([]);
        $grid->add('a', 'A');
        $grid->add('b', 'B');
        $grid->add('c', 'C');
        $grid->add('d', 'D');
        $grid->after('a')->add('aa', 'AA');
        $grid->remove('b', 'd');

        $this->assertEquals(
            ['A', 'AA', 'C'],
            collect($grid->cells())
                ->map(function (Cell $cell) {
                    return $cell->description();
                })
                ->values()
                ->toArray()
        );
    }
}
