<?php namespace Lego;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Lego\Command\IDEHelper;

/**
 * Lego Service Provider for Laravel
 */
class LegoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                IDEHelper::class,
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
        $this->publishes([$config => config_path('lego.php')]);
        $this->mergeConfigFrom($config, 'lego');

        // assets
        $this->publishes([
            $this->path('public/') => public_path(LegoAsset::ASSET_PATH)
        ]);

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

        $loader = AliasLoader::getInstance();
        $aliases = $loader->getAliases();

        if (!in_array('Html', $aliases)) {
            $loader->alias('Html', \Collective\Html\HtmlFacade::class);
        }

        if (!in_array('Form', $aliases)) {
            $loader->alias('Form', \Collective\Html\FormFacade::class);
        }
    }

    private function path($path = '')
    {
        return __DIR__ . '/../../' . $path;
    }
}