<?php

use Lego\Demo\Models\Suite;
use Lego\Lego;

$grid = Lego::gridV2(Suite::query());
$grid->add('id', 'ID', true);
$grid->add('status', 'Status', true);
$grid->add('address', 'Address');

return $grid;
