<?php
class DoubanOAuth extends POAuth
{
	function getLoginUrl()
	{
		$data = array(
				'response_type'=>'code', 'client_id'=>$this->appid, 'redirect_uri'=>self::$callback,
				//'scope'=>'',
		);
		return 'https://www.douban.com/service/auth2/auth?'.http_build_query($data);
	}

	function getUserInfo($code)
	{
		$data = array(
				'grant_type'=>'authorization_code', 'client_id'=>$this->appid, 'client_secret'=>$this->appkey,
				'code'=>$code, 'redirect_uri'=>self::$callback,
		);
		$r = PHttp::post('https://www.douban.com/service/auth2/token', $data);
		$r = json_decode($r, 1);
		$this->access_token = $r['access_token'];
// 		$rinfo = PHttp::get('https://api.douban.com/v2/user/'.$r['douban_user_id']);
// 		$rinfo = json_decode($rinfo, 1);
// 		var_dump($rinfo);
		$this->openid = $r['douban_user_id'];
		return array(
				'access_token'=>$this->access_token, 'expires_in'=>$r['expires_in'],
				'uid'=>$this->openid, 'name'=>$r['douban_user_name'], 'avatar'=>'',
				'refresh_token'=>$r['refresh_token'],
		);
	}
}