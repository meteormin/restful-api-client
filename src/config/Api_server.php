<?php

return [
    'host' => env('API_SERVER', 'http://127.0.0.1'),
    'module_namespace' => '\\Miniyus\\RestfulApiClient\\Api\\EndPoint',
    'token_storage' => [
        'storage' => ['name' => 'access_token'],
        'model' => ['name' => null],
        'session' => ['name' => 'access_token'],
        'cookie' => ['name' => 'access_token']
    ],
    'end_point' => []
];
