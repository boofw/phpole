<?php namespace Boofw\Phpole\Http;

use Boofw\Phpole\Helper\Arr;

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
            (self::$referer = self::refererInInput()) || (self::$referer = Arr::get($_SERVER, 'HTTP_REFERER', '/'));
        }
        return self::$referer;
    }

    static function refererInInput()
    {
        ($referer = Arr::get($_POST, 'referer')) || ($referer = urldecode(Arr::get($_GET, 'referer')));
        return $referer;
    }

    static function ip()
    {
        if (is_null(self::$ip)) {
            $forwards = explode(',', Arr::get($_SERVER, 'HTTP_X_FORWARDED_FOR', ''));
            self::$ip = trim(Arr::get($forwards, 0, ''));
            if ( ! self::$ip) self::$ip = Arr::get($_SERVER, 'HTTP_X_REAL_IP') ?: Arr::get($_SERVER, 'REMOTE_ADDR', '0.0.0.0');
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