<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Listeners;

use Molitor\Cms\Events\Post\PostDeleted;
use Molitor\CmsSearch\Services\PostIndexService;

class DeletePostIndexListener
{
    public function __construct(private PostIndexService $postIndexService) {}

    public function handle(PostDeleted $event): void
    {
        $this->postIndexService->deleteDocument($event->post->id);
    }
}
