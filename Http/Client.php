<?php namespace Boofw\Phpole\Http;

class Client
{
    private static function curl($method, $url, $data = [], $header = [], $cookie = '')
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

        $chinfo = curl_getinfo($ch);
        while ($chinfo['redirect_url']) {
            curl_setopt($ch, CURLOPT_URL, $chinfo['redirect_url']);
            $ret = curl_exec($ch);
            $chinfo = curl_getinfo($ch);
        }

        curl_close($ch);

        return $ret;
    }

    static function get($url, $data = [], $header = [], $cookie = '')
    {
        if ($data) {
            $url .= '?'.http_build_query($data);
        }
        return self::curl('GET', $url, [], $header, $cookie);
    }

    static function post($url, $data = [], $header = [], $cookie = '')
    {
        return self::curl('POST', $url, $data, $header, $cookie);
    }

    static function delete($url, $data = [], $header = [], $cookie = '')
    {
        return self::curl('DELETE', $url, $data, $header, $cookie);
    }

    static function raw($url, $raw = '', $args = [], $header = [], $cookie = '')
    {
        if ($args) {
            $url .= '?'.http_build_query($args);
        }
        return self::curl('POSTRAW', $url, $raw, $header, $cookie);
    }
}