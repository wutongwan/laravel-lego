<?php

use Illuminate\Support\Facades\Request;
use Lego\Demo\Models\City;
use Lego\Lego;


$city = City::findOrNew(Request::get('id'));

$form = Lego::form($city);
$form->addText('name', '城市名称')
    ->required() // 必填项目
    ->unique() // 城市名唯一
    // Laravel Validation Rules
    // 可以多次调用
    ->rule('not_in:foo,bar,test')
    ->rule('not_in:example')
    // 自定义的验证规则
    // 可以定义多个
    ->validator(function ($name) {
        return is_numeric($name) ? '城市名不可以为数字！' : true;
    })
    ->validator(function ($name) {
        return str_contains($name, ['-', '/', '~']) ? '城市名中包含非法字符！' : true;
    })
    ->placeholder('请输入城市名称')
    // 输入框下方的备注，没有进行转义，可以传入 HTML
    ->note('城市名唯一，不可重复');

$form->success(route('demo', 'city-list'));
$form->addRightTopButton('返回城市列表', route('demo', 'city-list'));

if ($city->id) {
    $form->addRightTopButton('下属街道列表', route('demo', 'street-list') . '?city-name=' . $city->name);
}

return $form;

