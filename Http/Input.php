<?php namespace Polev\Phpole\Http;

use Polev\Phpole\Helper\Str;
use Polev\Phpole\Helper\Arr;

class Input
{
    static $data = null;

    private static function init()
    {
        if (is_null(self::$data)) {
            self::$data = [];
            $input = Arr::dot($_REQUEST);
            foreach ($input as $k=>$v) {
                Arr::set(self::$data, $k, Str::e($v));
            }
        }
    }

    static function all()
    {
        self::init();
        return self::$data;
    }

    static function exists($k)
    {
        self::init();
        return Arr::has(self::$data, $k);
    }

    static function has($k)
    {
        return (bool) self::get($k);
    }

    static function get($k)
    {
        self::init();
        return Arr::get(self::$data, $k);
    }

    static function flash()
    {
        self::init();
        Session::flash('_old_input', self::$data);
    }

    static function old($k)
    {
        return Session::get('_old_input.'.$k);
    }

    static function file($k)
    {
        return Arr::get($_FILES, $k);
    }

    static function hasFile($k)
    {
        return Arr::has($_FILES, $k);
    }
}