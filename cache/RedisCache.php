<?php
class RedisCache
{
	static $cfg = array(
			'host'=>'127.0.0.1',
			'port'=>6379,
			'timeout'=>3,
			'reserved'=>NULL,
			'delay'=>0
	);
	
	static $redis;
	
	function __construct($cfg=array())
	{
		$cfg = array_merge(self::$cfg, $cfg);
		self::$redis = new Redis();
		self::$redis->connect($cfg['host'], $cfg['port'], $cfg['timeout'], $cfg['reserved'], $cfg['delay']);
	}
	
	function set($k, $v)
	{
		return self::$redis->set($k, $v);
	}
	
	function get($k)
	{
		return self::$redis->get($k);
	}
	
	function del($k)
	{
		return self::$redis->del($k);
	}
}