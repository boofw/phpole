<?php
class POAuth
{
	static $callback = '';
	static $cfg = array();
	
	protected $appid;
	protected $appkey;
	protected $access_token;
	protected $openid;
	
	public $sitename;
	
	static function init($site, $args=NULL)
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
		foreach (self::$cfg[$site] as $k=>$v) {
			$o->$k = $v;
		}
		if (is_array($args)) {
			$o->access_token = $args['token'];
			$o->openid = $args['rid'];
		}
		return $o;
	}
	
	/**
	 * 获取登录页地址
	 * @return 登录页地址
	 */
	function getLoginUrl()
	{
	}
	
	/**
	 * 获取登录用户相关信息
	 * @param string $code 回调返回的Authorization Code，一般为$_GET['code']
	 * @return array(
	 * 		'access_token' => '', 'expires_in' => '',
	 * 		'uid' => '', 'name' => '', 'avatar' => '',
	 * 		'refresh_token' => '',
	 * )
	 */
	function getUserInfo($code)
	{
	}
	
	/**
	 * 发微博
	 * @param $content 微博内容
	 * @param $imgpath 图片路径
	 * @param $pos {ip:ip, jing:位置经度, wei:位置纬度}
	 * @param $args
	 */
	function add_t($content, $imgpath = null, $pos = array(), $args = array())
	{
	}
	
	/**
	 * 加关注
	 * @param $rid 被关注用户openid
	 */
	function follow($rid)
	{
	}
}