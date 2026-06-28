<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Listeners;

use Molitor\Cms\Events\Post\PostUpdated;
use Molitor\CmsSearch\Services\PostIndexService;

class ReindexPostListener
{
    public function __construct(private PostIndexService $postIndexService) {}

    public function handle(PostUpdated $event): void
    {
        $this->postIndexService->indexModel($event->post);
    }
}
