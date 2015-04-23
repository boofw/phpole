<?php namespace Polev\Phpole\Http;

use Polev\Phpole\Helper\Arr;

class Session
{
    static function put($k, $v)
    {
        Arr::set($_SESSION, $k, $v);
    }

    static function flash($k, $v)
    {
        return self::put(self::makeFlashKey($k), $v);
    }

    static function has($k)
    {
        return Arr::has($_SESSION, $k) || Arr::has($_SESSION, self::makeFlashKey($k));
    }

    static function forget($k)
    {
        Arr::forget($k);
        Arr::forget(self::makeFlashKey($k));
    }

    static function all()
    {
        return $_SESSION;
    }

    static function get($k)
    {
        if (Arr::has(self::makeFlashKey($k))) {
            $v = Arr::get($_SESSION, self::makeFlashKey($k));
            self::forget($k);
        } else {
            $v = Arr::get($_SESSION, $k);
        }
        return $v;
    }

    static function pull($k)
    {
        $v = self::get($k);
        self::forget($k);
        return $v;
    }

    private static function makeFlashKey($k)
    {
        return '_flash.'.$k;
    }
}