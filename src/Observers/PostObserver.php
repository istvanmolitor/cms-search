<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Observers;

use Molitor\Cms\Models\Post;
use Molitor\CmsSearch\Services\PostIndexService;

class PostObserver
{
    public function __construct(private PostIndexService $indexService) {}

    public function created(Post $post): void
    {
        $this->indexService->indexModel($post);
    }

    public function updated(Post $post): void
    {
        $this->indexService->indexModel($post);
    }

    public function deleted(Post $post): void
    {
        $this->indexService->deleteDocument($post->id);
    }
}
