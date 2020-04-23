<?php

return [
    'environments'=>[
        'default_migration_table'=>'phinxlog',
        'default_database'=>'default',
        'default'=>[
            'adapter'=>'mysql',
            'host'=>env('DB_HOST'),
            'name'=>env('DB_NAME'),
            'user'=>env('DB_USER'),
            'pass'=>env('DB_PASS'),
            'table_prefix'=>env('DB_PREFIX', ''),
            'port'=>env('DB_PORT', '3306'),
            'charset'=>env('DB_CHARSET', 'utf8mb4'),
            'collation'=>env('DB_COLLATION', 'utf8mb4_unicode_ci')
        ]
    ]
];
