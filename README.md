# CMS Search Package

Elasticsearch integration for the CMS package. Provides indexing and searching capabilities for Page models.

## Features

- Index Page models to Elasticsearch
- Full-text search across page content
- Search by title, slug, lead text
- Filter by published status and language

## Installation

The package is auto-discovered by Laravel.

## Configuration

Configure Elasticsearch connection in your `.env` file:

```
ELASTICSEARCH_HOST=elasticsearch
ELASTICSEARCH_PORT=9200
ELASTICSEARCH_SCHEME=http
```

## Usage

### Indexing Pages

```php
use Molitor\CmsSearch\Services\PageSearchService;

$searchService = app(PageSearchService::class);

// Index a single page
$searchService->indexPage($page);

// Index all pages
$searchService->indexAllPages();

// Delete page from index
$searchService->deletePageFromIndex($pageId);

// Reindex all pages
$searchService->reindexAllPages();
```

### Searching Pages

```php
use Molitor\CmsSearch\Services\PageSearchService;

$searchService = app(PageSearchService::class);

// Simple search
$results = $searchService->search('query text');

// Search with options
$results = $searchService->search('query text', [
    'language_id' => 1,
    'is_published' => true,
    'page' => 1,
    'per_page' => 20
]);
```

## Artisan Commands

```bash
# Index all pages
php artisan cms-search:index-pages

# Reindex all pages (deletes and recreates index)
php artisan cms-search:reindex-pages
```

## Automatic Indexing

Pages are automatically indexed when:
- A page is created
- A page is updated
- A page is deleted

This is handled by the `PageObserver` class.

