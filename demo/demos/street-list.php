<?php


use Lego\Demo\Models\Street;
use Lego\Lego;

$filter = Lego::filter(Street::with('city'));
$filter->addText('city.name', '城市名称');
$filter->addText('name', '街道名称');
$filter->addDateRange('created_at', '创建时间');
$filter->addDateRange('updated_at', '最后一次更新时间');

$grid = Lego::grid($filter);
$grid->add('id', '操作')->pipe(function ($id) {
    return link_to(route('demo', 'street') . '?id=' . $id, '编辑');
});
$grid->add('city.name', '所属城市');
$grid->add('name', '名称');
$grid->add('created_at', '创建日期');
$grid->add('updated_at|time', '更新时间');

$grid->addLeftTopButton('新建街道', route('demo', 'street'));

return $grid;
