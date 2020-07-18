<?php

namespace Lego\Tests\Widget\Grid;

use Lego\Tests\Models\ExampleModel;
use Lego\Tests\TestCase;
use Lego\Widget\Grid\Grid;

class BatchTest extends TestCase
{
    public function testEnable()
    {
        $model = new ExampleModel();
        $model->setAttribute($model->getKeyName(), 233);
        $model->setAttribute('a', 'abc');

        $grid = new Grid([$model]);
        $grid->add('a', 'A');
        $grid->addBatch('Batch Test');

        $html = $this->render2html($grid);
        $this->assertStringContainsString('批处理模式', $html);
        $this->assertStringNotContainsString('Batch Test', $html);

        $grid->enableBatchMode();
        $grid->addBatch('Batch Test');
        $html = $this->render2html($grid);
        $this->assertStringContainsString('退出批处理', $html);
        $this->assertStringContainsString('Batch Test', $html);
    }

    public function testBatchIdName()
    {
        $grid = new Grid([]);
        self::assertEquals('id', $grid->getBatchIdName());

        $grid->setBatchIdName('uid');
        self::assertEquals('uid', $grid->getBatchIdName());
    }
}
