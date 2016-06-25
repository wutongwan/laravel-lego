<?php namespace Lego;

use Illuminate\Support\ServiceProvider;

/**
 * Lego Service Provider for Laravel
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
        $config = $this->path('config/lego.php');
        $this->publishes([$config => config_path('lego.php')]);
        $this->mergeConfigFrom($config, 'lego');

        // views
        $this->loadViewsFrom($this->path('views'), 'lego');
    }

    private function path($path = '')
    {
        return __DIR__ . '/../../' . $path;
    }
}