<?php namespace Polev\Phpole\Http;

class Client
{
    private static function curl($method, $url, $data = [], $header = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER , $header);
        }

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $ret =  curl_exec($ch);
        curl_close($ch);

        return $ret;
    }

    static function get($url, $data = [], $header = [])
    {
        if ($data) {
            $url .= '?'.http_build_query($data);
        }
        return self::curl('GET', $url, [], $header);
    }

    static function post($url, $data = [], $header = [])
    {
        return self::curl('POST', $url, $data, $header);
    }

    static function delete($url, $data = [], $header = [])
    {
        return self::curl('DELETE', $url, $data, $header);
    }
}