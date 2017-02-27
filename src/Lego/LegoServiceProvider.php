<?php namespace Lego;

use Illuminate\Support\ServiceProvider;
use Lego\Commands\GenerateIDEHelper;
use Lego\Commands\UpdateComponents;
use Lego\Foundation\Assets;
use Lego\Foundation\Fields;

/**
 * Lego Service Provider for Laravel
 */
class LegoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;
        if ($app->runningInConsole()) {
            $this->commands([
                GenerateIDEHelper::class,
                UpdateComponents::class,
            ]);
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
        $this->app->singleton('lego-fields', Fields::class);
        $this->app->singleton('lego-assets', Assets::class);
    }

    private function publishAssets()
    {
        $this->publishes(
            [
                $this->path('public/') => public_path(Assets::PATH_PREFIX),
            ],
            'public'
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
