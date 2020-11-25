<?php

use Lego\Demo\Models\Suite;
use Lego\Lego;

$form = Lego::form(Suite::query()->first());

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
$form->addNumber('number', 'Number')
    ->min(0)
    ->max(100)
    ->step(10);
$form->addHidden('hidden', 'hidden label')
    ->default('ha ha')
    ->formOnly();
$form->addAutoComplete('ac', 'Auto Complete')
    ->match(function () {
        return array_combine($keys = range(1, 100), $keys);
    })
    ->placeholder('this is placeholder')
    ->formOnly();
$form->addSelect('status', 'Select')
    ->options([
        '不可租' => [
            '待装修' => '待装修',
            '硬装中' => '硬装中',
        ],
        '可出租' => '可出租',
    ]);
$form->addColumnAutoComplete('type', 'Type');
$form->addOneToOneRelation('street.name', 'Street');
$form->addTextarea('textarea', 'Textarea')
    ->rows(10)
    ->formOnly();
$form->addRadios('radio', 'Radio')
    ->optionValues(['未签约', '待装修', '硬装中'])
    ->formOnly();
$form->addCheckboxes('checkbox', 'Checkbox')
    ->optionValues(['a', 'b', 'c'])
    ->formOnly();

return $form;
