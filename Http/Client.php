<?php namespace Boofw\Phpole\Http;

use ArrayObject;
use Boofw\Phpole\Exception\AppException;

class Client
{
    private static function curl($method, $url, $data = [], $header = [], $cookie = '', $traceRedirect = true)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER , $header);
        }

        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE , $cookie);
        }

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method === 'POSTRAW') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $ret =  curl_exec($ch);
        $header = curl_getinfo($ch);

        if ($ret === false) {
            throw new AppException(curl_error($ch), curl_errno($ch));
        }

        if ($traceRedirect) {
            while ($header['redirect_url']) {
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

    static function get($url, $data = [], $header = [], $cookie = '', $traceRedirect = true)
    {
        if ($data) {
            $url .= '?'.http_build_query($data);
        }
        return self::curl('GET', $url, [], $header, $cookie, $traceRedirect);
    }

    static function post($url, $data = [], $header = [], $cookie = '', $traceRedirect = true)
    {
        return self::curl('POST', $url, $data, $header, $cookie, $traceRedirect);
    }

    static function delete($url, $data = [], $header = [], $cookie = '', $traceRedirect = true)
    {
        return self::curl('DELETE', $url, $data, $header, $cookie, $traceRedirect);
    }

    static function raw($url, $raw = '', $args = [], $header = [], $cookie = '', $traceRedirect = true)
    {
        if ($args) {
            $url .= '?'.http_build_query($args);
        }
        return self::curl('POSTRAW', $url, $raw, $header, $cookie, $traceRedirect);
    }
}