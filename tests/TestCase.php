<?php

namespace Lego\Tests;

use Lego\LegoServiceProvider;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register(LegoServiceProvider::class);
        return $app;
    }
}
