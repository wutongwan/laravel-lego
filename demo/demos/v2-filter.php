<?php

use Lego\Demo\Models\Suite;
use Lego\Lego;
use Lego\Set\Form\Form;

$filter = Lego::filterV2(Suite::query());
$filter->addText('address', 'Address')
    ->whereStartsWith();

$filter->addRightTopButton('filter rt');
$filter->addRightBottomButton('filter rb');
$filter->addLeftTopButton('filter lt');
$filter->addLeftBottomButton('filter lb');

$grid = Lego::gridV2($filter);
$grid->add('id', 'ID', true);
$grid->add('status', 'Status', true);
$grid->add('address', 'Address');

$grid->addBatch('response')
    ->handle(function () {
        return redirect('/');
    });
$grid->addBatch('message')
    ->handle(function () {
        return 'hello world';
    });
$grid->addBatch('form')
    ->form(function (Form $form) {
        $form->addText('hello', 'world')->required();
    })
    ->handle(function () {
        $args = func_get_args();
        return 'hello world';
    });

$grid->addLeftTopButton('grid left top');
$grid->addRightTopButton('grid right top');

return $grid;
