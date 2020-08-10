<?php

use Illuminate\Support\Arr;
use Lego\Demo\Tools\ChinaRegions;
use Lego\Lego;

$regions = new ChinaRegions();

$form = Lego::form([
    'Province' => '120000000000',
    'City' => '120100000000',
    'County' => '120116000000',
    'Town' => '120101003000',
]);

$province = $form->addSelect('Province')
    ->options(Arr::pluck($regions->getProvinces(), 'name', 'id'))
    ->required();
$form->addCascadeSelect('City')
    ->depend($form->field('Province'), function ($provinceId) use ($regions) {
        return Arr::pluck($regions->getCities()[$provinceId] ?? [], 'name', 'id');
    })
    ->required();
$form->addCascadeSelect('County')
    ->depend($form->field('City'), function ($cityId) use ($regions) {
        return Arr::pluck($regions->getCounties()[$cityId] ?? [], 'name', 'id');
    })
    ->placeholder('行政区');
$form->addCascadeSelect('Town')
    ->depend($form->field('County'), function ($id) use ($regions) {
        return Arr::pluck($regions->getTowns()[$id] ?? [], 'name', 'id');
    });

//if (request()->ajax()) {
//    abort(500);
//}

$form->onSubmit(function () {
    return view('request-data');
});
return $form;
