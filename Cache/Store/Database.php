<?php namespace Polev\Phpole\Cache\Store;

use Polev\Phpole\Database\Database as Table;

class Database
{
    /**
     * @var \Polev\Phpole\Database\Database
     */
    private $handle;

    function __construct($database)
    {
        $this->handle = Table::init($database.'.sys_cache');
    }

    function put($k, $v, $minutes = 0)
    {
        $crts = time();
        $rmts = 0;
        if ($minutes > 0) {
            $rmts = $crts + $minutes * 60;
        }
        $this->handle->upsert(['k' => $k], compact('k', 'v', 'crts', 'rmts'));
    }

    function get($k)
    {
        $r = $this->handle->first(['k' => $k]);
        if ($r['rmts'] > 0 && time() > $r['rmts']) {
            return null;
        }
        return $r['v'];
    }

    function forget($k)
    {
        $this->handle->remove(['k' => $k]);
    }

    function increment($k, $step = 1)
    {
    }

    function decrement($k, $step = 1)
    {
    }
}