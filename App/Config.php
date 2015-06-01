<?php namespace Polev\Phpole\App;

use Polev\Phpole\Helper\Arr;

class Config
{
    static $config = null;

    static function init($config)
    {
        if (is_array($config)) {
            self::$config = $config;
        } elseif (is_string($config) && file_exists($config)) {
            self::$config = require $config;
        } else {
            self::$config = [];
        }

        $libs = self::get('libs');
        if (is_array($libs)) {
            foreach ($libs as $k => $v) {
                self::apply($k, $v);
            }
        }
    }

    static function get($k, $v = null)
    {
        return Arr::get(self::$config, $k, $v);
    }

    static function set($k, $v)
    {
        Arr::set(self::$config, $k, $v);
    }

    static function apply($class, $config = null)
    {
        if (is_null($config)) $config = self::get('libs.'.$class);
        foreach ($config as $k => $v) {
            $class::$$k = $v;
        }
    }
}