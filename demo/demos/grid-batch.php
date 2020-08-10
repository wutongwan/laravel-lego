<?php


use Illuminate\Support\Collection;
use Lego\Demo\Models\Suite;
use Lego\Lego;
use Lego\Widget\Form;

$grid = Lego::grid(Suite::class);
$grid->add('id', 'ID');
$grid->add('street.city.name', '城市');
$grid->add('street.name', '街道');
$grid->add('address', '地址');
$grid->add('type', '类型');
$grid->add('status', '状态');
$grid->add('created_at|date', '创建日期');
$grid->addLeftTopButton('新建公寓', route('demo', 'suite'));

// 1、一键批处理
$grid->addBatch('一键删除')
    ->each(function (Suite $suite) {
        $suite->delete();
    });

$grid->addBatch('房型汇总')
    ->handle(function (Collection $suites) {
        $message = $suites->groupBy('type')
            ->map(function ($suites, $type) {
                return $type . '：' . count($suites);
            })
            ->implode('，');
        return Lego::message($message);
    });

// 2、带提示信息的批处理
$grid->addBatch('批量删除-提示信息')
    ->message('确认删除 ？')
    ->each(function (Suite $suite) {
        $suite->delete();
    });

$grid->addBatch('批量删除-动态提示信息')
    ->message(function (Collection $suites) {
        return "确认删除公寓 <共 {$suites->count()} 条>？";
    })
    ->each(function (Suite $suite) {
        $suite->delete();
    });

// 3、带表单的批处理
$grid->addBatch('修改状态')
    ->openInNewTab()
    ->form(function (Form $form) use ($grid) {
        $form->addSelect('status', '公寓状态')
            ->values(Suite::listStatus())
            ->required();
    })
    ->each(function (Suite $suite, Form $form) {
        $suite->status = $form->field('status')->getNewValue();
        $suite->save();
    });

return $grid;
