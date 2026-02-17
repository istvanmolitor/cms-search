<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Providers;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Molitor\Cms\Models\Page;
use Molitor\CmsSearch\Console\Commands\IndexPagesCommand;
use Molitor\CmsSearch\Console\Commands\ReindexPagesCommand;
use Molitor\CmsSearch\Observers\PageObserver;
use Molitor\CmsSearch\Services\PageSearchService;

class CmsSearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/cms-search.php',
            'cms-search'
        );

        // Register Elasticsearch client as singleton
        $this->app->singleton(Client::class, function ($app) {
            $config = config('cms-search.connection');
            return ClientBuilder::create()
                ->setHosts($config['hosts'])
                ->build();
        });

        // Register PageSearchService as singleton
        $this->app->singleton(PageSearchService::class, function ($app) {
            return new PageSearchService(
                $app->make(Client::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/cms-search.php' => config_path('cms-search.php'),
            ], 'cms-search-config');

            // Register commands
            $this->commands([
                IndexPagesCommand::class,
                ReindexPagesCommand::class,
            ]);
        }

        // Register observer for automatic indexing
        Page::observe(PageObserver::class);
    }
}

