<?php

use Lego\Demo\Models\Suite;
use Lego\Lego;

$form = Lego::formV2(Suite::query()->first());

$form->addLeftTopButton('hello');
$form->addLeftBottomButton('world');
$form->addRightTopButton('hello');
$form->addRightBottomButton('world');

$form->addText('address', 'Address')
    ->accessor(function (Suite $suite, $value) {
        return $value . '...';
    })
    ->setInputName('hello')
    ->mutator(function (Suite $suite, $value) {
        $suite->address = trim($value, '.') . '+aa';
    });
$form->addHidden('hidden', 'hidden label')->default('ha ha')->formOnly();
$form->addAutoComplete('ac', 'auto complete')
    ->match(function () {
        return array_combine($keys = range(1, 100), $keys);
    })
    ->default(1000)
    ->formOnly();
$form->addColumnAutoComplete('status', 'Status');
$form->addOneToOneRelation('street.name', 'Street');

return $form;
