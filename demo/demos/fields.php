<?php

use Lego\Lego;

$form = Lego::form([]);

$form->addText('text');
$form->addTextarea('textarea');
$form->addRichText('richtext');

$form->addRadios('radios')->values(range('a', 'd'));
$form->addCheckboxes('checkboxes')->values(range('a', 'd'));

$form->addDate('date');
$form->addTime('time');
$form->addDatetime('datetime');
$form->addDateRange('date-range');
$form->addTimeRange('time-range');
$form->addDatetimeRange('datetime-range');

$form->addNumber('number');
$form->addNumberRange('number-range');

$form->addHidden('hidden');
$form->addJSON('json:key:subKey');
$form->addReadonly('readonly');

$form->addSelect('select')->values(range('a', 'z'));
$form->addSelect2('select2')->values(range('a', 'z'));
$form->addAutoComplete('auto-complete')->match(function ($keyword) {
    return array_map(function ($i) use ($keyword) {
        return $keyword . $i;
    }, range(1, 20));
});

return $form->onSubmit(function () {
    return view('lego-demo::request-data');
});
