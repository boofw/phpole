<?php namespace Boofw\Phpole\Http;

use Boofw\Phpole\Helper\Arr;

class Session
{
    private static $started = false;
    static $flash = [];

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
        self::put('_flash.'.$k, $v);
    }

    static function reflash()
    {
        self::init();
        foreach (self::$flash as $k => $v) {
            self::flash($k, $v);
        }
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

    static function get($k, $default = null)
    {
        self::init();
        return Arr::get(self::$flash, $k, Arr::get($_SESSION, $k, $default));
    }

    static function pull($k, $default = null)
    {
        $v = self::get($k, $default);
        self::forget($k);
        return $v;
    }
}