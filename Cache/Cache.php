<?php namespace Polev\Phpole\Cache;

use Polev\Phpole\Helper\Arr;
use Polev\Phpole\Exception\AppException;

class Cache
{
    /**
     * @var array $config
     * array(
           'default' => array(
               'driver' => 'database',
               'database' => 'default',
               'prefix' => '',
           ),
       );
     */
    static $config = [];

    private static $pool = [];

    /**
     * @var \Polev\Phpole\Cache\Store\Database
     */
    private $store;

    private function __construct($name)
    {
        if (array_key_exists($name, self::$config)) {
            $config = self::$config[$name];
            if ($config['driver'] === 'database') {
                $this->store = new \Polev\Phpole\Cache\Store\Database($config['database']);
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
     * @return \Polev\Phpole\Cache\Cache
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

    static function get($k)
    {
        $r = self::init()->store->get($k);
        return json_decode($r, 1);
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