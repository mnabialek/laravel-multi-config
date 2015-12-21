<?php

namespace Mnabialek\LaravelMultiConfig;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // create singleton so we can use it anywhere
        $this->app->singleton(MultiConfig::class, function ($app) {
            return new MultiConfig($app);
        });

        /** @var MultiConfig $multiConfig */
        $multiConfig = $this->app->make(MultiConfig::class);

        // publish default config into valid file
        $this->publishes([
            $multiConfig->getDefaultConfigFilePath() => $multiConfig->getConfigFilePath(),
        ]);
    }
}
