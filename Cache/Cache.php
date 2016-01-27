<?php namespace Boofw\Phpole\Cache;

use Boofw\Phpole\Helper\Arr;
use Boofw\Phpole\Exception\AppException;

class Cache
{
    static $config = array(
        'default' => array(
            'driver' => 'database',
            'database' => 'default.sys_cache',
            'prefix' => '',
        ),
    );

    private static $pool = [];

    /**
     * @var \Boofw\Phpole\Cache\Store\Database
     */
    private $store;

    private function __construct($name)
    {
        if (array_key_exists($name, self::$config)) {
            $config = self::$config[$name];
            if ($config['driver'] === 'database') {
                $this->store = new \Boofw\Phpole\Cache\Store\Database($config['database']);
            } else {
                throw new AppException('Cache driver <'.$config['driver'].'> not found!');
            }
        } else {
            throw new AppException('Cache handle <'.$name.'> not found!');
        }
    }

    /**
     * Cache init
     * @param $name
     * @return \Boofw\Phpole\Cache\Cache
     */
    static function init($name = null)
    {
        if (is_null($name)) $name = 'default';
        if (array_key_exists($name, self::$pool)) {
            return self::$pool[$name];
        }
        return self::$pool[$name] = new self($name);
    }

    static function put($k, $v, $minutes = 0)
    {
        self::init()->store->put($k, $v, $minutes);
    }

    static function forever($k, $v)
    {
        self::init()->store->put($k, $v);
    }

    static function get($k, $default = '')
    {
        $r = self::init()->store->get($k);
        return json_decode($r, 1) ?: $default;
    }

    static function forget($k)
    {
        self::init()->store->forget($k);
    }

    static function increment($k, $step = 1)
    {
        return self::init()->store->increment($k, $step);
    }

    static function decrement($k, $step = 1)
    {
        return self::init()->store->decrement($k, $step);
    }
}