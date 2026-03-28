<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Console\Commands;

use Illuminate\Console\Command;
use Molitor\CmsSearch\Services\PageSearchService;

class IndexPagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms-search:index-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index all pages to Elasticsearch';

    /**
     * Execute the console command.
     */
    public function handle(PageSearchService $searchService): int
    {
        $this->info('Starting to index pages...');

        $count = $searchService->indexAllPages();

        $this->info("Successfully indexed {$count} pages.");

        return self::SUCCESS;
    }
}
