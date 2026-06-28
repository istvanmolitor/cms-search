<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Providers;

use Elastic\Elasticsearch\Client;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Molitor\Cms\Events\Page\PageDeleted;
use Molitor\Cms\Events\Page\PageUpdated;
use Molitor\Cms\Events\Post\PostDeleted;
use Molitor\Cms\Events\Post\PostUpdated;
use Molitor\CmsSearch\Console\Commands\IndexPagesCommand;
use Molitor\CmsSearch\Console\Commands\IndexPostsCommand;
use Molitor\CmsSearch\Console\Commands\ReindexPagesCommand;
use Molitor\CmsSearch\Console\Commands\ReindexPostsCommand;
use Molitor\CmsSearch\Listeners\DeletePageIndexListener;
use Molitor\CmsSearch\Listeners\DeletePostIndexListener;
use Molitor\CmsSearch\Listeners\ReindexPageListener;
use Molitor\CmsSearch\Listeners\ReindexPostListener;
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
        Event::listen(PostUpdated::class, ReindexPostListener::class);
        Event::listen(PostDeleted::class, DeletePostIndexListener::class);
        Event::listen(PageUpdated::class, ReindexPageListener::class);
        Event::listen(PageDeleted::class, DeletePageIndexListener::class);

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

    }
}
