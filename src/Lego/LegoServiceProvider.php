<?php

namespace Lego;

use Illuminate\Support\ServiceProvider;
use Lego\Commands\GenerateIDEHelper;
use Lego\Field\FieldLoader;
use Lego\Foundation\Assets;

/**
 * Lego Service Provider for Laravel.
 */
class LegoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;
        if ($app->runningInConsole()) {
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
        $this->app->singleton('lego-assets', Assets::class);
    }

    private function publishAssets()
    {
        $this->publishes(
            [
                $this->path('public/') => public_path(Assets::PATH_PREFIX),
            ],
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
