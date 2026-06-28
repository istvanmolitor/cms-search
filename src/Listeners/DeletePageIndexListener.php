<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Listeners;

use Molitor\Cms\Events\Page\PageDeleted;
use Molitor\CmsSearch\Services\PageIndexService;

class DeletePageIndexListener
{
    public function __construct(private PageIndexService $pageIndexService) {}

    public function handle(PageDeleted $event): void
    {
        $this->pageIndexService->deleteDocument($event->page->id);
    }
}
