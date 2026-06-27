<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Observers;

use Molitor\Cms\Models\Page;
use Molitor\CmsSearch\Services\PageIndexService;

class PageObserver
{
    public function __construct(private PageIndexService $indexService) {}

    public function created(Page $page): void
    {
        $this->indexService->indexModel($page);
    }

    public function updated(Page $page): void
    {
        $this->indexService->indexModel($page);
    }

    public function deleted(Page $page): void
    {
        $this->indexService->deleteDocument($page->id);
    }
}
