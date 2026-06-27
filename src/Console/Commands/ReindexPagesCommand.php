<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Console\Commands;

use Illuminate\Console\Command;
use Molitor\CmsSearch\Services\PageIndexService;

class ReindexPagesCommand extends Command
{
    protected $signature = 'cms-search:reindex-pages';

    protected $description = 'Reindex all pages (deletes and recreates the index)';

    public function handle(PageIndexService $indexService): int
    {
        if (! $this->confirm('This will delete the existing pages index and recreate it. Continue?', true)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $this->info('Reindexing pages...');
        $count = $indexService->indexAllPages();
        $this->info("Reindexed {$count} pages.");

        return self::SUCCESS;
    }
}
