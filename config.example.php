<?php
return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'dbname' => 'rss_aggregator',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'memcached' => [
        'host' => '127.0.0.1',
        'port' => 11211,
        'prefix' => 'ria_rss:',
    ],
    'app' => [
        'base_url' => '/',
        'timezone' => 'Europe/Moscow',
        'items_per_page' => 20,
    ],
    'feeds' => [
        'ria_archive' => 'https://ria.ru/export/rss2/archive/index.xml',
    ],
];
