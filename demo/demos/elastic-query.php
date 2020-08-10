<?php


use Lego\Lego;
use Lego\Operator\Elastic\ElasticClient;

$filter = Lego::filter(new ElasticClient(['172.23.40.78:9200'], 'arm_daikan'));
$filter->addText('city', 'City Name');
$filter->addText('suite_address', 'Suite Address');

return $filter->grid(true);
