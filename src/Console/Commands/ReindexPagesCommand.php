<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Console\Commands;

use Illuminate\Console\Command;
use Molitor\CmsSearch\Services\PageSearchService;

class ReindexPagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms-search:reindex-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex all pages (deletes and recreates the index)';

    /**
     * Execute the console command.
     */
    public function handle(PageSearchService $searchService): int
    {
        if (! $this->confirm('This will delete the existing index and recreate it. Continue?', true)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $this->info('Deleting existing index...');
        $searchService->deleteIndex();

        $this->info('Creating new index...');
        $searchService->createIndex();

        $this->info('Indexing all pages...');
        $count = $searchService->indexAllPages();

        $this->info("Successfully reindexed {$count} pages.");

        return self::SUCCESS;
    }
}
