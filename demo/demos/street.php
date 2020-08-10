<?php

use Illuminate\Support\Facades\Request;
use Lego\Demo\Models\Street;
use Lego\Lego;

$street = Street::findOrNew(Request::get('id'));

$form = Lego::form($street);

if ($street->city_id) {
    $form->addRightBottomButton(
        '编辑城市：' . $street->city->name,
        route('demo', 'city') . '?id=' . $street->city_id
    );
}

$form->addAutoComplete('city.name', '所属城市')
    ->required();
//$form->addRichText('name', '街道名称')
$form->addText('name', '街道名称')
    ->required()
    ->unique();

$form->success(route('demo', 'street-list'));

return $form;
