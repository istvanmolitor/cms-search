<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Connection
    |--------------------------------------------------------------------------
    |
    | Configure your Elasticsearch connection settings here.
    |
    */

    'connection' => [
        'hosts' => [
            [
                'host' => env('ELASTICSEARCH_HOST', 'localhost'),
                'port' => env('ELASTICSEARCH_PORT', 9200),
                'scheme' => env('ELASTICSEARCH_SCHEME', 'http'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Index Settings
    |--------------------------------------------------------------------------
    |
    | Configure index names and settings for different models.
    |
    */

    'indices' => [
        'pages' => [
            'name' => env('ELASTICSEARCH_PAGES_INDEX', 'cms_pages'),
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'default' => [
                            'type' => 'standard',
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                    ],
                    'slug' => [
                        'type' => 'keyword',
                    ],
                    'lead' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                    ],
                    'content' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                    ],
                    'is_published' => [
                        'type' => 'boolean',
                    ],
                    'language_id' => [
                        'type' => 'integer',
                    ],
                    'layout' => [
                        'type' => 'keyword',
                    ],
                    'main_image_url' => [
                        'type' => 'keyword',
                    ],
                    'created_at' => [
                        'type' => 'date',
                    ],
                    'updated_at' => [
                        'type' => 'date',
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Settings
    |--------------------------------------------------------------------------
    |
    | Configure default search behavior.
    |
    */

    'search' => [
        'default_per_page' => 20,
        'max_per_page' => 100,
    ],
];
