<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Listeners;

use Molitor\Cms\Events\Page\PageUpdated;
use Molitor\CmsSearch\Services\PageIndexService;

class ReindexPageListener
{
    public function __construct(private PageIndexService $pageIndexService) {}

    public function handle(PageUpdated $event): void
    {
        $this->pageIndexService->indexModel($event->page);
    }
}
