<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Console\Commands;

use Illuminate\Console\Command;
use Molitor\CmsSearch\Services\PostIndexService;

class IndexPostsCommand extends Command
{
    protected $signature = 'cms-search:index-posts';

    protected $description = 'Index all posts to Elasticsearch';

    public function handle(PostIndexService $indexService): int
    {
        $this->info('Indexing posts...');
        $count = $indexService->indexAllPosts();
        $this->info("Indexed {$count} posts.");

        return self::SUCCESS;
    }
}
