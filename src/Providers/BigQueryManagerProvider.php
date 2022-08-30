<?php
namespace Juanparati\LaravelBQ\Providers;

use Illuminate\Support\ServiceProvider;
use Juanparati\LaravelBQ\BigQueryManager;
use Juanparati\LaravelBQ\Commands\GenerateCredentialsCommand;


/**
 * Service provider
 */
class BigQueryManagerProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/bigquery.php' => config_path('bigquery.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([GenerateCredentialsCommand::class]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/bigquery.php', 'bigquery');

        $this->app->singleton(BigQueryManager::class, function($app) {
            return new BigQueryManager($app['config']['bigquery']);
        });

        $this->app->alias(BigQueryManager::class, 'bigquery');
    }
}