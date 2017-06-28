<?php namespace Lego\Tests\Widget\Grid;

use Illuminate\Support\Facades\Config;
use Lego\Tests\TestCase;
use Lego\Tests\Tools\FakeMobileDetect;
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
}
