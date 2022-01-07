<?php

$config =
    Array(
        'database' => [
            'host'      =>  getenv('DB_HOST'),
            'port'      =>  getenv('DB_PORT'),
            'username'  =>  getenv('DB_USERNAME'),
            'password'  =>  getenv('DB_PASSWORD'),
            'dbname'    =>  getenv('DB_DATABASE')
        ],
        'influxDB' => [
            'api_key'   =>  getenv('API_TOKEN')
        ],
        'redis' => [
            'host'      =>  'localhost',
            'port'      =>  9000,
            'prefix'    =>  'phalconBasix-',
            'lifetime'  =>  1200
        ]
);