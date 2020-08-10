<?php

use Illuminate\Support\Collection;
use Lego\Demo\Models\Suite;
use Lego\Lego;

$filter = Lego::filter(Suite::query());
$filter->addText('street.city.name', '城市');
$filter->addText('address', '地址');
$filter->addSelect('type', '公寓类型')->values(Suite::listType());
$filter->addSelect('status', '公寓状态')->values(Suite::listStatus());
$filter->addDateRange('created_at', '创建时间');

$grid = Lego::grid($filter);
$grid->add('address', '地址')
    ->link(route('demo', 'suite') . '?id={id}');
// same as
//$grid->add('address', '地址')->pipe(function ($address, Suite $suite) {
//    return link_to(route('demo', 'suite') . '?id=' . $suite->id, $address);
//});
$grid->add('status', '状态');
$grid->add('street.city.name', '城市');
$grid->add('type', '类型');
$grid->add('created_at|date', '创建日期');
$grid->paginate(10);

$grid->addBatch('变更状态')
    ->form(function (\Lego\Widget\Form $form) {
        $form->addSelect('status', '状态')
            ->values(Suite::listStatus())
            ->required();
    })
    ->each(function (Suite $suite, \Lego\Widget\Form $form) {
        $suite->status = $form->field('status')->getNewValue();
        $suite->save();
    });

return $grid;
