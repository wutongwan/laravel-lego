<?php
// zhangwei@danke.com

use Lego\Demo\Models\Suite;
use Lego\Lego;

$filter = Lego::filterV2(Suite::query());
$filter->addText('address', 'Address');

return $filter;
