<?php namespace Boofw\Phpole\Http;

use ArrayObject;
use Boofw\Phpole\Exception\AppException;

class Client
{
    public static function curl($method, $url, $data = [], $header = [], $cookie = '', $traceRedirect = false)
    {
        $method = strtoupper($method);
        if ('GET' === $method && is_array($data) && $data) {
            if (parse_url($url, PHP_URL_QUERY)) {
                $url .= '&'.http_build_query($data);
            } else {
                $url .= '?'.http_build_query($data);
            }
            $data = [];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER , $header);
        }

        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE , $cookie);
        }

        if ('UPLOAD' === $method) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ('GET' !== $method) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
            }
        }

        $ret =  curl_exec($ch);
        $header = curl_getinfo($ch);

        if ($ret === false) {
            throw new AppException(curl_error($ch), curl_errno($ch));
        }

        if ($traceRedirect) {
            while (array_key_exists('redirect_url', $header) && $header['redirect_url']) {
                curl_setopt($ch, CURLOPT_URL, $header['redirect_url']);
                $ret = curl_exec($ch);
                $header = curl_getinfo($ch);
            }
        }

        curl_close($ch);

        return new ArrayObject(array(
            'header' => $header,
            'body' => $ret,
        ), ArrayObject::ARRAY_AS_PROPS);
    }

    public static function get($url, $data = [], $header = [], $cookie = '', $traceRedirect = false)
    {
        return self::curl('GET', $url, $data, $header, $cookie, $traceRedirect);
    }

    public static function post($url, $data = [], $header = [], $cookie = '', $traceRedirect = false)
    {
        return self::curl('POST', $url, $data, $header, $cookie, $traceRedirect);
    }

    public static function delete($url, $data = [], $header = [], $cookie = '', $traceRedirect = false)
    {
        return self::curl('DELETE', $url, $data, $header, $cookie, $traceRedirect);
    }

    public static function put($url, $data = [], $header = [], $cookie = '', $traceRedirect = false)
    {
        return self::curl('PUT', $url, $data, $header, $cookie, $traceRedirect);
    }

    public static function upload($url, $data = [], $header = [], $cookie = '', $traceRedirect = false)
    {
        return self::curl('UPLOAD', $url, $data, $header, $cookie, $traceRedirect);
    }
}
