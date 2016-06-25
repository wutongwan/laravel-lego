<?php namespace Lego;

use Illuminate\Support\ServiceProvider;

/**
 * Laravel 注册类
 */
class LegoServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // config file
        $config = __DIR__ . '/../../config/lego.php';
        $this->publishes([$config => config_path('lego.php')]);
        $this->mergeConfigFrom($config, 'lego');
    }
}