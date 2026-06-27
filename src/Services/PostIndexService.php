<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Services;

use Illuminate\Database\Eloquent\Model;
use Molitor\Cms\Models\Post;
use Molitor\Cms\Repositories\PostRepositoryInterface;
use Molitor\Search\Services\AbstractElasticsearchService;

class PostIndexService extends AbstractElasticsearchService
{
    protected function getIndexName(): string
    {
        return config('cms-search.indices.posts.name', 'cms_posts');
    }

    protected function getSettings(): array
    {
        return config('cms-search.indices.posts.settings');
    }

    protected function getMappings(): array
    {
        return config('cms-search.indices.posts.mappings');
    }

    protected function buildSearchQuery(string $query, array $options): array
    {
        $must = [];
        $filter = [];

        if (! empty($query)) {
            $must[] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title^3', 'lead^2', 'content', 'slug'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO',
                ],
            ];
        }

        if (isset($options['language_id'])) {
            $filter[] = ['term' => ['language_id' => $options['language_id']]];
        }

        if (isset($options['is_published'])) {
            $filter[] = ['term' => ['is_published' => $options['is_published']]];
        }

        return [
            'query' => [
                'bool' => [
                    'must' => $must ?: [['match_all' => (object) []]],
                    'filter' => $filter,
                ],
            ],
            'sort' => [
                '_score' => ['order' => 'desc'],
                'created_at' => ['order' => 'desc'],
            ],
        ];
    }

    protected function prepareDocument(Model $model): array
    {
        /** @var Post $model */
        return [
            'title' => $model->title,
            'slug' => $model->slug,
            'lead' => $model->lead,
            'content' => $this->extractContentText($model),
            'is_published' => $model->is_published,
            'language_id' => $model->language_id,
            'layout' => $model->layout,
            'main_image_url' => $model->main_image_url,
            'created_at' => $model->created_at?->toIso8601String(),
            'updated_at' => $model->updated_at?->toIso8601String(),
        ];
    }

    public function indexAllPosts(): int
    {
        return $this->reindexAll(function (callable $callback) {
            Post::with(['content', 'language'])->chunk(100, $callback);
        });
    }

    private function extractContentText(Post $post): string
    {
        return app(PostRepositoryInterface::class)->toString($post);
    }
}
