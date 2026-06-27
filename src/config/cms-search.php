<?php

return [
    'indices' => [
        'pages' => [
            'name' => env('ELASTICSEARCH_PAGES_INDEX', 'cms_pages'),
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'default' => ['type' => 'standard'],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'title' => ['type' => 'text', 'analyzer' => 'standard'],
                    'slug' => ['type' => 'keyword'],
                    'lead' => ['type' => 'text', 'analyzer' => 'standard'],
                    'content' => ['type' => 'text', 'analyzer' => 'standard'],
                    'is_published' => ['type' => 'boolean'],
                    'language_id' => ['type' => 'integer'],
                    'layout' => ['type' => 'keyword'],
                    'main_image_url' => ['type' => 'keyword'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                ],
            ],
        ],

        'posts' => [
            'name' => env('ELASTICSEARCH_POSTS_INDEX', 'cms_posts'),
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'default' => ['type' => 'standard'],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'title' => ['type' => 'text', 'analyzer' => 'standard'],
                    'slug' => ['type' => 'keyword'],
                    'lead' => ['type' => 'text', 'analyzer' => 'standard'],
                    'content' => ['type' => 'text', 'analyzer' => 'standard'],
                    'is_published' => ['type' => 'boolean'],
                    'language_id' => ['type' => 'integer'],
                    'layout' => ['type' => 'keyword'],
                    'main_image_url' => ['type' => 'keyword'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                ],
            ],
        ],
    ],
];
