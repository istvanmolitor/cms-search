<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Providers;

use Elastic\Elasticsearch\Client;
use Illuminate\Support\ServiceProvider;
use Molitor\Cms\Models\Page;
use Molitor\Cms\Models\Post;
use Molitor\CmsSearch\Console\Commands\IndexPagesCommand;
use Molitor\CmsSearch\Console\Commands\IndexPostsCommand;
use Molitor\CmsSearch\Console\Commands\ReindexPagesCommand;
use Molitor\CmsSearch\Console\Commands\ReindexPostsCommand;
use Molitor\CmsSearch\Observers\PageObserver;
use Molitor\CmsSearch\Observers\PostObserver;
use Molitor\CmsSearch\Services\PageIndexService;
use Molitor\CmsSearch\Services\PostIndexService;

class CmsSearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cms-search.php', 'cms-search');

        $this->app->singleton(PageIndexService::class, fn ($app) => new PageIndexService($app->make(Client::class)));
        $this->app->singleton(PostIndexService::class, fn ($app) => new PostIndexService($app->make(Client::class)));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cms-search.php' => config_path('cms-search.php'),
            ], 'cms-search-config');

            $this->commands([
                IndexPagesCommand::class,
                ReindexPagesCommand::class,
                IndexPostsCommand::class,
                ReindexPostsCommand::class,
            ]);
        }

        Page::observe(PageObserver::class);
        Post::observe(PostObserver::class);
    }
}
