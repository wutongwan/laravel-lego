<?php

use Lego\Demo\Models\Suite;
use Lego\Lego;

$form = Lego::formV2(Suite::query()->first());

$form->addText('address', 'Address')
    ->accessor(function (Suite $suite, $value) {
        return $value . '...';
    })
    ->setInputName('hello')
    ->mutator(function (Suite $suite, $value) {
        $suite->address = trim($value, '.') . '+aa';
        dump($suite);
    });

return $form;
