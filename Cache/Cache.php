<?php namespace Polev\Phpole\Cache;

use Polev\Phpole\Helper\Arr;
use Polev\Phpole\Exception\AppException;

class Cache
{
    /**
     * @var array $config
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
        $this->store->put($k, $v, $minutes);
    }

    static function forever($k, $v)
    {
        $this->store->put($k, $v);
    }

    static function get($k)
    {
        $r = $this->store->get($k);
        return json_decode($r, 1);
    }

    static function forget($k)
    {
        $this->store->forget($k);
    }

    static function increment($k, $step = 1)
    {
        return $this->store->increment($k, $step);
    }

    static function decrement($k, $step = 1)
    {
        return $this->store->decrement($k, $step);
    }
}