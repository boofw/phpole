<?php
class PApiClient
{
	protected $apiUrl;
	protected $appId;
	protected $appKey;
	protected $service;

	private static $_obj;

	static function init($apiUrl, $appId, $appKey, $service)
	{
		if (!(self::$_obj instanceof self)) {
			self::$_obj = new self();
		}
		self::$_obj->apiUrl = $apiUrl;
		self::$_obj->appId = $appId;
		self::$_obj->appKey = $appKey;
		self::$_obj->service = $service;
		return self::$_obj;
	}

	private function __construct()
	{
	}

	function __call($method, $args)
	{
		$argsjson = json_encode($args);

		$token = self::mkHash($argsjson, $this->appId, $this->appKey, $this->service);

		$postdata = array(
				'appid' => $this->appId,
				'service' => $this->service,
				'method' => $method,
				'token' => $token,
				'args' => $argsjson,
		);

		$ch = curl_init($this->apiUrl);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$res = curl_exec($ch);
		curl_close($ch);

// 		print_r($res);

		return json_decode($res, true);

	}

	static function mkHash($args, $appid, $appkey, $service)
	{
		return sha1($args.')|^'.$appid.'(|*'.$appkey.'%|$'.$service);
	}

}