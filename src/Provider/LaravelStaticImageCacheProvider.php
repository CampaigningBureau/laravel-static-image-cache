<?php

namespace CampaigningBureau\LaravelStaticImageCache\Provider;

use Illuminate\Support\ServiceProvider;
use CampaigningBureau\LaravelStaticImageCache\Classes\ImageProxy;
use CampaigningBureau\LaravelStaticImageCache\Commands\ClearStaticCache;

class LaravelStaticImageCacheProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');

        $this->publishes([
            __DIR__.'/../../config/static-image-cache.php' => config_path('static-image-cache.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/static-image-cache.php', 'static-image-cache');

        $this->app->singleton('static-image-cache', function() {
            return new ImageProxy();
        });

        $this->commands([
            ClearStaticCache::class,
        ]);
    }

    /**
     * Load the given routes file if routes are not already cached.
     *
     * @param  string $path
     *
     * @return void
     */
    protected function loadRoutesFrom($path)
    {
        if (!$this->app->routesAreCached()) {
            require $path;
        }
    }

    public function provides()
    {
        return [
            'static-image-cache'
        ];
    }
}
