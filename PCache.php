<?php
class PCache
{
	/**
	 * @var string 配置存储方式
	 */
	static $driver = 'redis';
	/**
	 * @var array 对应存储驱动的详细配置，具体参考相应驱动
	 * 驱动为 'redis' 需配 array('host'=>'127.0.0.1','port'=>6379,'timeout'=>3,'reserved'=>NULL,'delay'=>0)
	 */
	static $cfg = array();
	
	static function init()
	{
		$c = ucfirst(self::$driver).'Cache';
		if (!class_exists($c) && file_exists(__DIR__.'/cache/'.$c.'.php')) {
			require __DIR__.'/cache/'.$c.'.php';
		}
		$o = new $c(self::$cfg);
		return $o;
	}
	
	static function set($k, $v)
	{
		return self::init()->set($k, $v);
	}
	
	static function get($k, $v=NULL)
	{
		$r = self::init()->get($k);
		if (!$r) $r = $v;
		return $r;
	}
	
	static function del($k)
	{
		return self::init()->del($k);
	}
	
	static function getdel($k)
	{
		$r = self::init()->get($k);
		self::init()->del($k);
		return $r;
	}
}