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
    ->handle(function (array $rows) {
        return 'hello count: ' . count($rows);
    });
$grid->addBatch('form')
    ->form(function (Form $form) {
        $form->addText('name', 'Name')->required();
    })
    ->handle(function (array $rows, array $formData) {
        return 'hello ' . $formData['name'] . ', rows count: ' . count($rows);
    });
$grid->addBatch('confirm')
    ->confirm('hello world')
    // or
    ->confirm(function (array $rows) {
        return 'total count: ' . count($rows);
    })
    ->handle(function (array $rows) {
        return 'confirmed';
    });

$grid->addLeftTopButton('grid left top');
$grid->addRightTopButton('grid right top');

return $grid;
