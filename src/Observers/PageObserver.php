<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Observers;

use Molitor\Cms\Models\Page;
use Molitor\CmsSearch\Services\PageSearchService;

class PageObserver
{
    private PageSearchService $searchService;

    public function __construct(PageSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Handle the Page "created" event.
     */
    public function created(Page $page): void
    {
        $this->searchService->indexPage($page);
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page): void
    {
        $this->searchService->indexPage($page);
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        $this->searchService->deletePageFromIndex($page->id);
    }
}

