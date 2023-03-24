<?php
namespace Ey\Client;

/**
 * 简单的请求工具
 *
 * @param string $method
 * @param string $url
 * @param array $options
 * @return false|string
 */
function request($method, $url, $options = [])
{
    // 随机国内ip
    if (! empty($options['enable_rand_ip'])) {
        $ip = rand_ip();
        $options['headers']['client-ip'] = $ip;
        $options['headers']['x-forwarded-for'] = $ip;
    }

    $split = "\r\n";
    $opts = [
        'method' => strtoupper($method),
        'timeout' => empty($options['timeout']) ? 10 : (int)$options['timeout'],
    ];

    if (empty($options['headers']) || ! is_array($options['headers'])) {
        $options['headers'] = [];
    } else {
        $options['headers'] = array_change_key_case($options['headers'], CASE_LOWER);
    }

    if (! empty($options['base_uri']) && ! preg_match('#^https?:\/\/#i', $url)) {
        $url = $options['base_uri'] . $url;
    }

    if (! empty($options['query'])) {
        $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query((array)$options['query']);
    }

    if (! empty($options['body'])) {
        if (is_resource($options['body'])) {
            $options['headers']['content-type'] = mime_content_type($options['body']);
            $opts['content'] = stream_get_contents($options['body']);
            $options['body'] && fclose($options['body']);
            unset($options['body']);
        } else {
            $opts['content'] = (string)$options['body'];
        }
    } elseif (! empty($options['form_params'])) {
        $opts['content'] = http_build_query((array)$options['form_params']);
    } elseif (! empty($options['multipart'])) {
        $boundary = 'Ey-' . md5(microtime());
        $options['headers']['content-type'] = 'multipart/form-data; boundary=' . $boundary;
        $contents = [];
        foreach ((array)$options['multipart'] as $k => $v) {
            $content = "--{$boundary}{$split}Content-Disposition: form-data; name=\"{$k}\"";
            if (is_string($v) && strpos($v, '@') === 0 && is_file($f = substr($v, 1))) {
                $v = fopen($f, 'rb');
            }

            if (is_resource($v)) {
                $fMeta = stream_get_meta_data($v);
                $content .= '; filename="' . basename($fMeta['uri']) . "\"{$split}";
                $content .= 'Content-Type: ' . mime_content_type($v) . "{$split}";
                $content .= "{$split}{$split}" . stream_get_contents($v);
                $v && fclose($v);
            } else {
                $content .= "{$split}{$split}{$v}";
            }

            $contents[] = $content;
        }

        if ($contents) {
            $contents[] = "--{$boundary}--";
            $opts['content'] = implode($split, $contents);
        }
    } elseif (! empty($options['json'])) {
        $options['headers']['content-type'] = 'application/json; charset=UTF-8';
        $opts['content'] = is_string($options['json']) ? $options['json'] : json_encode($options['json']);
    }

    if (empty($options['headers']) || ! is_array($options['headers'])) {
        $options['headers'] = [];
    }

    // 设置默认用户信息
    if (empty($options['headers']['user-agent'])) {
        $options['headers']['user-agent'] = 'Ey-Client/0.1.0';
    }

    // 带内容时需要设置类型
    if (! empty($opts['content']) && empty($options['headers']['content-type'])) {
        $options['headers']['content-type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
    }

    $header = [];
    foreach ($options['headers'] as $k => $v) {
        $header[] = "{$k}:{$v}";
    }

    $opts['header'] = implode($split, $header);

    $context = stream_context_create(['http' => $opts]);

    return file_get_contents($url, false, $context);
}

/**
 * @return string
 */
function rand_ip()
{
    $ipLong = [
        ['607649792', '608174079'], //36.56.0.0-36.63.255.255
        ['1038614528', '1039007743'], //61.232.0.0-61.237.255.255
        ['1783627776', '1784676351'], //106.80.0.0-106.95.255.255
        ['2035023872', '2035154943'], //121.76.0.0-121.77.255.255
        ['2078801920', '2079064063'], //123.232.0.0-123.235.255.255
        ['-1950089216', '-1948778497'], //139.196.0.0-139.215.255.255
        ['-1425539072', '-1425014785'], //171.8.0.0-171.15.255.255
        ['-1236271104', '-1235419137'], //182.80.0.0-182.92.255.255
        ['-770113536', '-768606209'], //210.25.0.0-210.47.255.255
        ['-569376768', '-564133889'], //222.16.0.0-222.95.255.255
    ];

    $randKey = mt_rand(0, 9);

    return long2ip(mt_rand($ipLong[$randKey][0], $ipLong[$randKey][1]));
}

/**
 * 格式化请求参数
 *
 * @param array $options
 * @return array
 */
function format_options(array $options)
{
    if (empty($options)) {
        return [];
    }

    if (isset($options['body']) ||
        isset($options['query']) ||
        isset($options['json']) ||
        isset($options['multipart']) ||
        isset($options['form_params'])
    ) {
        return $options;
    }

    $name = 'query';
    switch (empty($options['headers']['content-type']) ? '' : $options['headers']['content-type']) {
        case 'application/json':
            $name = 'json';
            break;
        case 'multipart/form-data':
            $name = 'multipart';
            unset($options['headers']['content-type']); // boundary 参数需要由组件生成，此处要删除内容类型
            break;
        case 'application/x-www-form-urlencoded':
            $name = 'form_params';
            break;
    }

    foreach ($options as $key => $value) {
        if (!in_array($key, ['timeout', 'base_uri', 'enable_rand_ip', 'headers'])) {
            $options[$name][trim($key, '"')] = $value;
            unset($options[$key]);
        }
    }

    return $options;
}