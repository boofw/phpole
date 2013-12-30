<?php
class POAuth
{
	static $callback = '';
	static $cfg = array();
	
	protected $appid;
	protected $key;
	protected $access_token;
	protected $openid;
	
	static function init($site)
	{
		if (class_exists('PCfg')) {
			PCfg::apply(__CLASS__);
		}
		$driver = ucfirst(strtolower($site)).'OAuth';
		if (!class_exists($driver)) {
			require dirname(__FILE__).'/oauth/'.$driver.'.php';
		}
		if (!class_exists($driver)) {
			throw new Exception('OAuth Driver `'.$driver.'` not found');
		}
		$o = new $driver();
		foreach (self::$cfg as $k=>$v) {
			$o->$k = $v;
		}
		return $o;
	}
	
	/**
	 * 获取登录页地址
	 * @param string $callbackurl 回调页Url
	 * @return 登录页地址
	 */
	function getLoginUrl($callbackurl)
	{
	}
	
	/**
	 * 获取登录用户相关信息
	 * @param array $request 回调返回的内容，一般为$_REQUEST
	 * @return array(
	 * 		'access_token' => '', 'expires_in' => '',
	 * 		'uid' => '', 'name' => '', 'avatar' => '',
	 * 		'refresh_token' => '',
	 * )
	 */
	function getUserInfo($request = null)
	{
	}
	
	/**
	 * 发微博
	 * @param $content 微博内容
	 * @param $img 图片
	 * @param $pos {ip:ip, jing:位置经度, wei:位置纬度}
	 * @param $args
	 */
	function add_t($content, $img = null, $pos = array(), $args = array())
	{
	}
	
	/**
	 * 加关注
	 * @param $rid 被关注用户id
	 */
	function follow($rid)
	{
	}
}