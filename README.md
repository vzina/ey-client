## 简单的PHP客户端

### 安装
```shell
composer install vzina/ey-client
```

### 使用
```php
use function Ey\Client\request;

require dirname(__DIR__) . '/vendor/autoload.php';

$data = request('POST', '/index.php', [
    // 根路径
    'base_uri' => 'http://127.0.0.1:8080',
    // 设置头信息
    'headers' => [
        // 'content-type' => 'application/json; charset=UTF-8',
    ],
    // query string
    'query' => [
        'a' => 'b',
    ],
    // 文件上传
    'multipart' => [
        'name' => 'test',
        'file' => fopen(__DIR__ . '/upload.txt', 'rb'), // 或：'@' . __DIR__ . '/upload.txt'
    ],
    // json请求
    // 'json' => [
    //     'c' => 'd'
    // ],
    // raw请求
    // 'body' => json_encode([
    //     'c' => 'd'
    // ])
    // 文件流请求
    // 'body' => fopen(__DIR__ . '/upload.txt', 'rb'),
]);

$data = request('GET', 'http://127.0.0.1:8080/');

var_dump($data);
```
