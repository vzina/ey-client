<?php

use function Ey\Client\request;

require dirname(__DIR__) . '/vendor/autoload.php';

$data = request('POST', '/index.php', [
    'base_uri' => 'http://127.0.0.1:8080',
    'headers' => [
        // 'content-type' => 'application/json; charset=UTF-8',
    ],
    'query' => [
        'a' => 'b',
    ],
    'multipart' => [
        'name' => 'test',
        'file' => fopen(__DIR__ . '/upload.txt', 'rb'),
    ],
    // 'json' => [
    //     'c' => 'd'
    // ],
    // 'body' => json_encode([
    //     'c' => 'd'
    // ])
    // 'body' => fopen(__DIR__ . '/3.txt', 'rb'),
]);

var_dump($data);