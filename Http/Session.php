<?php namespace Polev\Phpole\Http;

use Polev\Phpole\Helper\Arr;

class Session
{
    private static $started = false;
    private static $flash = [];

    private static function init()
    {
        if ( ! self::$started) {
            session_start();
            self::$started = true;
            self::$flash = Arr::get($_SESSION, '_flash', []);
            Arr::forget($_SESSION, '_flash');
        }
    }

    static function put($k, $v)
    {
        self::init();
        Arr::set($_SESSION, $k, $v);
    }

    static function flash($k, $v)
    {
        return self::put('_flash.'.$k, $v);
    }

    static function has($k)
    {
        self::init();
        return Arr::has($_SESSION, $k) || Arr::has(self::$flash, $k);
    }

    static function forget($k)
    {
        self::init();
        Arr::forget($_SESSION, $k);
        Arr::forget(self::$flash, $k);
    }

    static function all()
    {
        self::init();
        return $_SESSION;
    }

    static function get($k)
    {
        self::init();
        return Arr::get(self::$flash, $k, Arr::get($_SESSION, $k));
    }

    static function pull($k)
    {
        $v = self::get($k);
        self::forget($k);
        return $v;
    }
}