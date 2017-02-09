<?php namespace Lego;

use Illuminate\Support\ServiceProvider;
use Lego\Commands\GenerateIDEHelper;
use Lego\Commands\UpdateComponents;

/**
 * Lego Service Provider for Laravel
 */
class LegoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
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
        // config
        $config = $this->path('config/lego.php');
        $this->publishes([$config => config_path('lego.php')], 'config');
        $this->mergeConfigFrom($config, 'lego');

        // assets
        $this->publishes(
            [$this->path('public/') => public_path(LegoAsset::ASSET_PATH)],
            'public'
        );

        // views
        $this->loadViewsFrom($this->path('resources/views'), 'lego');

        // ** 第三方库 **
        $this->registerHtmlServices();
    }

    /**
     * 依赖第三方库 laravelcollective/html, 为方便使用, 在这里自动注册
     */
    private function registerHtmlServices()
    {
        $this->app->register(\Collective\Html\HtmlServiceProvider::class);
    }

    private function path($path = '')
    {
        return __DIR__ . '/../../' . $path;
    }
}
