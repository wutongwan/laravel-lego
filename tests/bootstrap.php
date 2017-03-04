<?php

use Carbon\Carbon;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('UTC');

Carbon::setTestNow(Carbon::now());
