<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Console\Commands;

use Illuminate\Console\Command;
use Molitor\CmsSearch\Services\PostIndexService;

class ReindexPostsCommand extends Command
{
    protected $signature = 'cms-search:reindex-posts';

    protected $description = 'Reindex all posts (deletes and recreates the index)';

    public function handle(PostIndexService $indexService): int
    {
        if (! $this->confirm('This will delete the existing posts index and recreate it. Continue?', true)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $this->info('Reindexing posts...');
        $count = $indexService->indexAllPosts();
        $this->info("Reindexed {$count} posts.");

        return self::SUCCESS;
    }
}
