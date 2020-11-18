<?php
// zhangwei@danke.com

use Lego\Demo\Models\Suite;
use Lego\Lego;

$filter = Lego::filterV2(Suite::query());
$filter->addText('address', 'Address')
    ->whereStartsWith();

$grid = Lego::gridV2($filter);
$grid->add('id', 'ID', true);
$grid->add('status', 'Status', true);
$grid->add('address', 'Address');

return $grid;
