<?php

return [
    'default' => env("DB_CONNECTION", "mysql"),
    'drivers' => [
        'mysql' => [
            'host' => env("DB_HOST", "localhost"),
            'user' => env("DB_USER"),
            'db' => env("DB_DBNAME"),
            'password' => env("DB_PASSWORD"),
            'port' => env("DB_PORT", 3306)
        ],
    ]
];
