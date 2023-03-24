<?php

namespace Ey\Client\Traits;

use function Ey\Client\format_options;
use function Ey\Client\request;

trait WithHttpClientMethods
{
    public function get($url, array $options = [])
    {
        return request('GET', $url, format_options($options));
    }

    public function post($url, array $options = [])
    {
        return request('POST', $url, format_options($options));
    }

    public function postJson($url, array $options = [])
    {
        $options['headers']['content-type'] = 'application/json';

        return $this->post($url, $options);
    }

    public function postXml($url, array $options = [])
    {
        $options['headers']['content-type'] = 'text/xml';

        return $this->post($url, $options);
    }

    public function postFile($url, array $options = [])
    {
        $options['headers']['content-type'] = 'multipart/form-data';

        return $this->post($url, $options);
    }

    public function patch($url, array $options = [])
    {
        return request('PATCH', $url, format_options($options));
    }

    public function patchJson($url, array $options = [])
    {
        $options['headers']['content-type'] = 'application/json';

        return $this->patch($url, $options);
    }

    public function put($url, array $options = [])
    {
        return request('PUT', $url, format_options($options));
    }

    public function delete($url, array $options = [])
    {
        return request('DELETE', $url, format_options($options));
    }
}