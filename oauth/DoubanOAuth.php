<?php
class DoubanOAuth extends POAuth
{
	function getLoginUrl($callbackurl) {
		$data = array(
				'response_type'=>'code', 'client_id'=>$this->appid, 'redirect_uri'=>$callbackurl,
		);
		return 'https://www.douban.com/service/auth2/auth?'.http_build_query($data);
	}

	function getUserInfo($request = null) {
		if (!$request) $request = $_REQUEST;
		$data = array(
				'grant_type'=>'authorization_code', 'client_id'=>$this->appid, 'client_secret'=>$this->key,
				'code'=>$request['code'], 'redirect_uri'=>$request['callbackurl'],
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

	function add_t($content, $img = null, $ip = null, $jing = null, $wei = null) {

	}

}