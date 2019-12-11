<?php

namespace Serendipias\Urn;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Serendipias\Urn\Services\UrnService;

class UrnServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        $source = realpath($raw = __DIR__.'/../config/urn.php') ?: $raw;
        if ($this->app instanceof LaravelApplication) {
            $this->publishes([$source => config_path('urn.php')], 'urn');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('urn');
        }

        $this->mergeConfigFrom($source, 'urn');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/urn.php', 'urn'
        );

        $this->app->singleton(UrnService::class, function ($app) {
            return new UrnService(
                $app['config']['urn.service'],
                $app['config']['urn.stage'],
                $app['config']['urn.models_namespace']
            );
        });
    }

    public function provides()
    {
        return [UrnService::class];
    }
}
