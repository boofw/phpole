<?php namespace Polev\Phpole\Http;

use Polev\Phpole\Helper\Arr;

class Request
{
    static $ajax = null;
    static $referer = null;
    static $ip = null;
    static $domain = null;

    static function ajax()
    {
        if (is_null(self::$ajax)) {
            self::$ajax = (array_key_exists('ajax', $_REQUEST) || Arr::get($_SERVER, 'HTTP_X_REQUESTED_WITH')==='XMLHttpRequest');
        }
        return self::$ajax;
    }

    static function referer()
    {
        if (is_null(self::$referer)) {
            (self::$referer = Arr::get($_POST, 'referer')) || (self::$referer = urldecode(Arr::get($_GET, 'referer'))) || (self::$referer = Arr::get($_SERVER, 'HTTP_REFERER', '/'));
        }
        return self::$referer;
    }

    static function ip()
    {
        if (is_null(self::$ip)) {
            self::$ip = Arr::get($_SERVER, 'HTTP_X_REAL_IP') ?: Arr::get($_SERVER, 'REMOTE_ADDR', '0.0.0.0');
        }
        return self::$ip;
    }

    static function domain()
    {
        if (is_null(self::$domain)) {
            self::$domain = Arr::get($_SERVER, 'HTTP_X_REAL_HOST') ?: Arr::get($_SERVER, 'HTTP_HOST', '');
        }
        return self::$domain;
    }
}