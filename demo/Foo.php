<?php

use Ey\Client\Traits\WithHttpClientMethods;

require dirname(__DIR__) . '/vendor/autoload.php';

class Foo
{
    use WithHttpClientMethods;
}

$foo = new Foo();

// $data = $foo->get('http://127.0.0.1:8080/');
// $data = $foo->post('http://127.0.0.1:8080/', ['json' => ['a' => 1234]]);
$data = $foo->postJson('http://127.0.0.1:8080/', ['a' => 1234]);
// $data = $foo->patch('http://127.0.0.1:8080/', ['json' => ['a' => 1234]]);
// $data = $foo->patchJson('http://127.0.0.1:8080/', ['a' => 1234]);
// $data = $foo->postFile('http://127.0.0.1:8080/', ['a' => 1234, 'file' => fopen(__DIR__ . '/upload.txt', 'rb')]);
// $data = $foo->delete('http://127.0.0.1:8080/', ['a' => 1234]);

var_dump($data);