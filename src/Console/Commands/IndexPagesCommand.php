<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Console\Commands;

use Illuminate\Console\Command;
use Molitor\CmsSearch\Services\PageIndexService;

class IndexPagesCommand extends Command
{
    protected $signature = 'cms-search:index-pages';

    protected $description = 'Index all pages to Elasticsearch';

    public function handle(PageIndexService $indexService): int
    {
        $this->info('Indexing pages...');
        $count = $indexService->indexAllPages();
        $this->info("Indexed {$count} pages.");

        return self::SUCCESS;
    }
}
