<?php

namespace Lego;

use Illuminate\Support\ServiceProvider;
use Lego\Commands\GenerateIDEHelper;
use Lego\Field\FieldLoader;

/**
 * Lego Service Provider for Laravel.
 */
class LegoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([GenerateIDEHelper::class]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->publishConfigs();
        $this->publishAssets();

        // views
        $this->loadViewsFrom($this->path('resources/views'), 'lego');

        // alias
        $this->app->singleton('lego-fields', FieldLoader::class);
    }

    private function publishAssets()
    {
        $this->publishes(
            [$this->path('public/') => public_path('packages/wutongwan/lego')],
            'lego-assets'
        );
    }

    private function publishConfigs()
    {
        $config = $this->path('config/lego.php');
        $this->publishes([$config => config_path('lego.php')], 'config');
        $this->mergeConfigFrom($config, 'lego');
    }

    private function path($path = '')
    {
        return __DIR__ . '/../../' . $path;
    }
}
