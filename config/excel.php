<?php

use Maatwebsite\Excel\Excel;

return [
    'exports' => [
        'chunk_size' => 1000,
        'pre_calculate_formulas' => false,
        'strict_null_comparison' => false,
    ],
    'imports' => [
        'read_only' => true,
        'heading_row' => [
            'formatter' => 'slug',
        ],
    ],
    'extension_detector' => [
        'xlsx' => Excel::XLSX,
        'xls' => Excel::XLS,
        'csv' => Excel::CSV,
    ],
    'value_binder' => [
        'enabled' => false,
    ],
    'cache' => [
        'enable' => false,
        'ttl' => 600,
    ],
    'transactions' => [
        'handler' => 'db',
    ],
    'temporary_files' => [
        'local_path' => storage_path('framework/cache'),
        'local_prefix' => 'laravel-excel',
    ],
];
