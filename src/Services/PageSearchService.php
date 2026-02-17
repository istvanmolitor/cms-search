<?php

declare(strict_types=1);

namespace Molitor\CmsSearch\Services;

use Elasticsearch\Client;
use Illuminate\Support\Collection;
use Molitor\Cms\Models\Page;

class PageSearchService
{
    private Client $client;
    private string $indexName;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->indexName = config('cms-search.indices.pages.name', 'cms_pages');
    }

    /**
     * Create the pages index with mappings
     */
    public function createIndex(): bool
    {
        if ($this->indexExists()) {
            return false;
        }

        $params = [
            'index' => $this->indexName,
            'body' => [
                'settings' => config('cms-search.indices.pages.settings'),
                'mappings' => config('cms-search.indices.pages.mappings'),
            ],
        ];

        $this->client->indices()->create($params);

        return true;
    }

    /**
     * Check if index exists
     */
    public function indexExists(): bool
    {
        return $this->client->indices()->exists(['index' => $this->indexName]);
    }

    /**
     * Delete the pages index
     */
    public function deleteIndex(): bool
    {
        if (!$this->indexExists()) {
            return false;
        }

        $this->client->indices()->delete(['index' => $this->indexName]);

        return true;
    }

    /**
     * Index a single page
     */
    public function indexPage(Page $page): void
    {
        if (!$this->indexExists()) {
            $this->createIndex();
        }

        $params = [
            'index' => $this->indexName,
            'id' => $page->id,
            'body' => $this->preparePageData($page),
        ];

        $this->client->index($params);
    }

    /**
     * Delete a page from the index
     */
    public function deletePageFromIndex(int $pageId): void
    {
        if (!$this->indexExists()) {
            return;
        }

        try {
            $params = [
                'index' => $this->indexName,
                'id' => $pageId,
            ];

            $this->client->delete($params);
        } catch (\Exception $e) {
            // Document might not exist, ignore
        }
    }

    /**
     * Index all pages
     */
    public function indexAllPages(): int
    {
        if (!$this->indexExists()) {
            $this->createIndex();
        }

        $count = 0;
        Page::with(['content', 'language'])
            ->chunk(100, function (Collection $pages) use (&$count) {
                foreach ($pages as $page) {
                    $this->indexPage($page);
                    $count++;
                }
            });

        return $count;
    }

    /**
     * Reindex all pages (delete and recreate index)
     */
    public function reindexAllPages(): int
    {
        $this->deleteIndex();
        $this->createIndex();

        return $this->indexAllPages();
    }

    /**
     * Search for pages
     *
     * @param string $query Search query
     * @param array $options Additional search options:
     *   - language_id: Filter by language
     *   - is_published: Filter by published status
     *   - page: Page number (default: 1)
     *   - per_page: Results per page (default: 20)
     *
     * @return array Array with 'data', 'total', 'page', 'per_page'
     */
    public function search(string $query, array $options = []): array
    {
        if (!$this->indexExists()) {
            return [
                'data' => [],
                'total' => 0,
                'page' => 1,
                'per_page' => $options['per_page'] ?? config('cms-search.search.default_per_page', 20),
            ];
        }

        $page = $options['page'] ?? 1;
        $perPage = min(
            $options['per_page'] ?? config('cms-search.search.default_per_page', 20),
            config('cms-search.search.max_per_page', 100)
        );

        $must = [];
        $filter = [];

        // Add text search query
        if (!empty($query)) {
            $must[] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title^3', 'lead^2', 'content', 'slug'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO',
                ],
            ];
        }

        // Add filters
        if (isset($options['language_id'])) {
            $filter[] = [
                'term' => ['language_id' => $options['language_id']],
            ];
        }

        if (isset($options['is_published'])) {
            $filter[] = [
                'term' => ['is_published' => $options['is_published']],
            ];
        }

        // Build query
        $body = [
            'query' => [
                'bool' => [
                    'must' => $must ?: [['match_all' => (object)[]]],
                    'filter' => $filter,
                ],
            ],
            'from' => ($page - 1) * $perPage,
            'size' => $perPage,
            'sort' => [
                '_score' => ['order' => 'desc'],
                'created_at' => ['order' => 'desc'],
            ],
        ];

        $params = [
            'index' => $this->indexName,
            'body' => $body,
        ];

        try {
            $response = $this->client->search($params);

            $hits = $response['hits']['hits'] ?? [];
            $total = $response['hits']['total']['value'] ?? 0;

            $data = array_map(function ($hit) {
                return array_merge(
                    ['id' => $hit['_id']],
                    $hit['_source'],
                    ['score' => $hit['_score']]
                );
            }, $hits);

            return [
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
            ];
        } catch (\Exception $e) {
            // Log error and return empty results
            \Log::error('Elasticsearch search error: ' . $e->getMessage());

            return [
                'data' => [],
                'total' => 0,
                'page' => $page,
                'per_page' => $perPage,
            ];
        }
    }

    /**
     * Prepare page data for indexing
     */
    private function preparePageData(Page $page): array
    {
        // Extract text content from content elements
        $contentText = '';
        if ($page->content && $page->content->contentRegions) {
            foreach ($page->content->contentRegions as $region) {
                if ($region->contentElements) {
                    foreach ($region->contentElements as $element) {
                        // Extract text based on element type
                        if (isset($element->data['text'])) {
                            $contentText .= strip_tags($element->data['text']) . ' ';
                        } elseif (isset($element->data['content'])) {
                            $contentText .= strip_tags($element->data['content']) . ' ';
                        }
                    }
                }
            }
        }

        return [
            'title' => $page->title,
            'slug' => $page->slug,
            'lead' => $page->lead,
            'content' => trim($contentText),
            'is_published' => $page->is_published,
            'language_id' => $page->language_id,
            'layout' => $page->layout,
            'main_image_url' => $page->main_image_url,
            'created_at' => $page->created_at?->toIso8601String(),
            'updated_at' => $page->updated_at?->toIso8601String(),
        ];
    }
}

