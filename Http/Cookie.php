<?php namespace Polev\Phpole\Http;

use Polev\Phpole\Helper\Arr;

class Cookie
{
    static $path = '/';
    static $domain = '';
    static $secure = false;
    static $httponly = false;

    static function put($k, $v, $minutes = 0)
    {
        $expire = 0;
        if ($minutes) {
            $expire = time() + $minutes * 60;
        }
        setcookie($k, $v, $expire, self::$path, self::$domain, self::$secure, self::$httponly);
    }

    static function forever($k, $v)
    {
        self::put($k, $v, 60*24*365*100);
    }

    static function get($k)
    {
        return Arr::get($_COOKIE, $k);
    }

    static function forget($k)
    {
        self::put($k, '', -60*24);
    }
}