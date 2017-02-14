<?php namespace Lego;

use Illuminate\Support\ServiceProvider;
use Lego\Commands\GenerateIDEHelper;
use Lego\Commands\UpdateComponents;
use Lego\Foundation\Fields;
use Lego\Register\Data\UserDefinedField;

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

        $this->app->singleton(Fields::class, Fields::class);

        // views
        $this->loadViewsFrom($this->path('resources/views'), 'lego');

        $this->registerUserDefinedFields();
    }

    private function path($path = '')
    {
        return __DIR__ . '/../../' . $path;
    }

    private function registerUserDefinedFields()
    {
        foreach (config('lego.user-defined-fields') as $field) {
            lego_register(UserDefinedField::class, $field);
        }
    }
}
